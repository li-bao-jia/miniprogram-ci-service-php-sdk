<?php

declare(strict_types=1);

namespace LiBaoJia\MiniprogramCiServicePhpSdk;

final class Config
{
    private string $baseUrl;
    private string $token;
    private float $timeout;
    private float $connectTimeout;

    public function __construct(
        string $baseUrl,
        string $token = '',
        float $timeout = 60.0,
        float $connectTimeout = 10.0
    ) {
        $baseUrl = trim($baseUrl);
        if ($baseUrl === '') {
            throw new \InvalidArgumentException('baseUrl can not be empty');
        }

        $this->baseUrl = rtrim($baseUrl, '/');
        $this->token = trim($token);
        $this->timeout = $timeout > 0 ? $timeout : 60.0;
        $this->connectTimeout = $connectTimeout > 0 ? $connectTimeout : 10.0;
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
}
