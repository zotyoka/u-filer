<?php

namespace Zotyo\uFiler;

use Illuminate\Support\ServiceProvider;
use Validator;
use Route;

class PackageServiceProvider extends ServiceProvider
{
    const CONFIG_NAME = 'uFiler';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('VerifyFileByToken', UploadedFileValidator::class.'@verifyFileByToken');

        $this->publishes([
            __DIR__.'/assets/uploads' => public_path($this->configGet('relative_path')),
            __DIR__.'/assets/js' => resource_path('/assets/js'),
            ], 'public');

        $this->publishes([
            $this->getMyConfigPath() => config_path(self::CONFIG_NAME.'.php'),
            ], 'config');
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom($this->getMyConfigPath(), self::CONFIG_NAME);

        if ($this->configGet('route.enabled')) {
            Route::post($this->configGet('route.url'), \Zotyo\uFiler\Http\UploadController::class.'@upload');
        }
    }

    private function configGet($key)
    {
        return config(self::CONFIG_NAME.'.'.$key);
    }

    private function getMyConfigPath()
    {
        return __DIR__.'/config/config.php';
    }
}