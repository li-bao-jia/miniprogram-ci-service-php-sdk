# miniprogram-ci-service-php-sdk

`miniprogram-ci-service` 的 PHP SDK，用于在 PHP 项目中统一调用小程序上传和预览接口。

## 安装

### 1. Packagist（后续发布）

```bash
composer require li-bao-jia/miniprogram-ci-service-php-sdk
```

### 2. 本地联调（path repository）

在你的项目 `composer.json` 中增加：

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "../miniprogram-ci-service-php-sdk",
      "options": {
        "symlink": true
      }
    }
  ]
}
```

然后安装：

```bash
composer require li-bao-jia/miniprogram-ci-service-php-sdk:*
```

## 快速开始

```php
<?php

use LiBaoJia\MiniprogramCiServicePhpSdk\Client;
use LiBaoJia\MiniprogramCiServicePhpSdk\Config;
use LiBaoJia\MiniprogramCiServicePhpSdk\Exception\ApiException;

$config = new Config(
    'http://127.0.0.1:3000',
    '',      // token，可选
    60.0,    // timeout
    10.0,    // connect timeout
    1,       // retry times
    300,     // retry interval ms
    [-38]    // retryable business error codes（如微信“编译中”）
);

$client = new Client($config);

try {
    $result = $client->upload([
        'type' => 'miniProgram',
        'appid' => 'wx1234567890',
        'projectPath' => '/data/code/applet/your-project',
        'privateKeyPath' => '/data/key/private.wx1234567890.key',
        'version' => '1.0.0',
        'desc' => 'CI auto upload',
        'robot' => 1,
        'threads' => 3,
    ]);

    var_dump($result);
} catch (ApiException $e) {
    // 可通过 getResponseData() 获取服务端返回数据
    var_dump($e->getMessage(), $e->getResponseData());
}
```

## API

### `upload(array $payload): array`

调用 `POST /upload` 上传小程序代码。

### `preview(array $payload): array`

调用 `POST /preview` 预览接口（取决于服务端实现）。

## 异常说明

SDK 在以下情况会抛出 `ApiException`：

- 网络请求失败（含重试后失败）
- HTTP 状态码非 2xx
- 响应不是合法 JSON
- 服务端返回 `state=false`
- 服务端返回 `code != 0` / `errCode != 0`（含 `data` 内嵌字段）
