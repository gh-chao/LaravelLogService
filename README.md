# laravel-wechat

阿里云LogService的laravel集成

## 框架要求

Laravel/Lumen >= 5.1

## 安装

```shell
composer require "lilocon/laravel-log-service"
```

## 配置

1. 在 `config/app.php` 注册 ServiceProvider (Laravel 5.5 无需手动注册)

```php
'providers' => [
    // ...
    Lilocon\LaravelLogService\ServiceProvider::class,
],
```

2. 创建配置文件和assets：

```shell
php artisan vendor:publish --provider="Lilocon\LaravelLogService\ServiceProvider"
```

3. 修改应用根目录下的 `config/log-service.php` 中对应的参数即可。

## 日志收集

laravel5.5使用

修改bootstrap/app.php文件，对应位置添加如下代码

```php
$app->configureMonologUsing(function ($logger) {
    (new Lilocon\LaravelLogService\Logging\Factory)($logger);
});
```

laravel5.6使用

修改config/logging.php文件， 添加自定义channel

```
    'LogService' => [
        'driver' => 'custom',
        'via' => \Lilocon\LaravelLogService\Logging\CreateLogger::class,
    ],
```

## 日志查看

修改app/Providers/RouteServiceProvider.php文件
添加路由即可
```
Route::any('/log-service-viewer', 'Lilocon\LaravelLogService\Controller\ViewerController@index');
```
