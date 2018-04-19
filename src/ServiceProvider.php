<?php

namespace Lilocon\LaravelLogService;

use Aliyun\SLS\Client;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../resource/views', 'log-service');


        $this->publishes([
            __DIR__ . '/../config/log-service.php' => config_path('log-service.php'),
        ], 'config');

        $this->publishes([
            __DIR__ . '/../resource/views' => resource_path('views/vendor/log-service'),
        ], 'views');

        $this->publishes([
            __DIR__ . '/../resource/public' => public_path('vendor/log-service'),
        ], 'public');

    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/log-service.php', 'log-service'
        );

        $this->app->singleton('log-service.client', function () {
            $config = $this->app['config']['log-service'];
            return new Client($config['endpoint'], $config['accessKeyId'], $config['accessKey']);
        });
        $this->app->bind(Client::class, 'log-service.client');
    }
}
