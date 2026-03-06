<?php

declare(strict_types=1);

namespace LiBaoJia\MiniprogramCiServicePhpSdk;

final class Config
{
    private string $baseUrl;
    private string $token;
    private float $timeout;
    private float $connectTimeout;
    private int $retryTimes;
    private int $retryIntervalMs;
    /** @var int[] */
    private array $retryableBizCodes;

    public function __construct(
        string $baseUrl,
        string $token = '',
        float $timeout = 60.0,
        float $connectTimeout = 10.0,
        int $retryTimes = 0,
        int $retryIntervalMs = 300,
        array $retryableBizCodes = [-38]
    ) {
        $baseUrl = trim($baseUrl);
        if ($baseUrl === '') {
            throw new \InvalidArgumentException('baseUrl can not be empty');
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = trim($token);
        $this->timeout = $timeout > 0 ? $timeout : 60.0;
        $this->connectTimeout = $connectTimeout > 0 ? $connectTimeout : 10.0;
        $this->retryTimes = $retryTimes >= 0 ? $retryTimes : 0;
        $this->retryIntervalMs = $retryIntervalMs >= 0 ? $retryIntervalMs : 0;
        $this->retryableBizCodes = array_values(array_map('intval', $retryableBizCodes));
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTimeout(): float
    {
        return $this->timeout;
    }

    public function getConnectTimeout(): float
    {
        return $this->connectTimeout;
    }

    public function getRetryTimes(): int
    {
        return $this->retryTimes;
    }

    public function getRetryIntervalMs(): int
    {
        return $this->retryIntervalMs;
    }

    /**
     * @return int[]
     */
    public function getRetryableBizCodes(): array
    {
        return $this->retryableBizCodes;
    }
}
