<?php
namespace Zotyo\uFiler;

use Illuminate\Support\ServiceProvider;
use Validator;
use Route;

class PackageServiceProvider extends ServiceProvider
{
    const CONFIG_NAME = 'u-filer';

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('VerifyFileByToken', UploadedFileValidator::class.'@verifyFileByToken');

        $this->publishes([
            __DIR__.'/assets/uploads' => public_path(config(self::CONFIG_NAME.'.relative_path')),
            __DIR__.'/assets/js'      => resource_path('/assets/js'),
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

        if (config(self::CONFIG_NAME.'.route.enabled')) {
            Route::post(config(self::CONFIG_NAME.'.route.url'), \Zotyo\uFiler\Http\UploadController::class.'@upload')
                ->name(config(self::CONFIG_NAME.'.route.name'));
        }
    }

    private function getMyConfigPath()
    {
        return __DIR__.'/config/config.php';
    }
}
