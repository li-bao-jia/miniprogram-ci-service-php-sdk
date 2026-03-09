<?php

declare(strict_types=1);

namespace LiBaoJia\MiniprogramCiServicePhpSdk;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use LiBaoJia\MiniprogramCiServicePhpSdk\Exception\ApiException;
use Psr\Http\Message\ResponseInterface;

class Client
{
    private Config $config;
    private GuzzleClient $httpClient;

    public function __construct(Config $config, ?GuzzleClient $httpClient = null)
    {
        $this->config = $config;
        $this->httpClient = $httpClient ?: new GuzzleClient([
            'base_uri' => $config->getBaseUrl() . '/',
            'timeout' => $config->getTimeout(),
            'connect_timeout' => $config->getConnectTimeout(),
            'http_errors' => false,
        ]);
    }

    public function upload(array $payload): array
    {
        return $this->post('upload', $payload);
    }

    public function preview(array $payload): array
    {
        return $this->post('preview', $payload);
    }

    private function post(string $path, array $payload): array
    {
        try {
            $response = $this->httpClient->request('POST', ltrim($path, '/'), [
                'headers' => $this->buildHeaders(),
                'json' => $payload,
            ]);
        } catch (GuzzleException $e) {
            throw new ApiException(
                'HTTP request failed: ' . $e->getMessage(),
                0,
                [],
                $e
            );
        }

        return $this->parseResponse($response);
    }

    private function parseResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $body = (string) $response->getBody();

        if ($body === '') {
            throw new ApiException('Empty response body', $statusCode);
        }

        $decoded = json_decode($body, true);
        if (!is_array($decoded)) {
            throw new ApiException('Invalid JSON response: ' . $body, $statusCode);
        }

        if ($statusCode < 200 || $statusCode >= 300) {
            throw new ApiException($this->extractErrorMessage($decoded, 'HTTP request failed'), $statusCode, $decoded);
        }

        if ($this->isBusinessFailure($decoded)) {
            throw new ApiException($this->extractErrorMessage($decoded, 'Business failed'), $statusCode, $decoded);
        }

        return $decoded;
    }

    private function isBusinessFailure(array $decoded): bool
    {
        if (array_key_exists('state', $decoded) && $decoded['state'] === false) {
            return true;
        }

        if (array_key_exists('code', $decoded) && is_numeric($decoded['code']) && (int)$decoded['code'] !== 0) {
            return true;
        }

        if (array_key_exists('errCode', $decoded) && is_numeric($decoded['errCode']) && (int)$decoded['errCode'] !== 0) {
            return true;
        }

        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $data = $decoded['data'];
            if (array_key_exists('code', $data) && is_numeric($data['code']) && (int)$data['code'] !== 0) {
                return true;
            }
            if (array_key_exists('errCode', $data) && is_numeric($data['errCode']) && (int)$data['errCode'] !== 0) {
                return true;
            }
        }

        return false;
    }

    private function extractErrorMessage(array $decoded, string $fallback): string
    {
        $candidates = [
            $decoded['message'] ?? null,
            $decoded['msg'] ?? null,
            $decoded['errMsg'] ?? null,
        ];
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $candidates[] = $decoded['data']['message'] ?? null;
            $candidates[] = $decoded['data']['msg'] ?? null;
            $candidates[] = $decoded['data']['errMsg'] ?? null;
        }

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && trim($candidate) !== '') {
                return trim($candidate);
            }
        }

        return $fallback;
    }

    private function buildHeaders(): array
    {
        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $token = $this->config->getToken();
        if ($token !== '') {
            $headers['Authorization'] = 'Bearer ' . $token;
        }

        return $headers;
    }

}
