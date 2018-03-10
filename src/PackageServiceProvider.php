<?php
namespace Zotyo\uFiler;

use Illuminate\Support\ServiceProvider;
use Route;
use Zotyo\uFiler\Repositories\LocalFileSystemRepository;
use Zotyo\uFiler\Repositories\AwsS3Repository;

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
        $this->publishes([
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
            Route::post($this->cfg('route.url'), \Zotyo\uFiler\Http\UploadController::class.'@upload')
                ->name($this->cfg('route.name'));
        }

        switch ($this->cfg('repo')) {
            case 'local':
                app()->singletion(Repository::class, new LocalFileSystemRepository(
                    $this->cfg('repos.local.dir'),
                    $this->cfg('prefix'),
                    $this->cfg('repos.local.baseUrl')
                ));
                break;
            case 'aws':
                app()->singletion(Repository::class, new AwsS3Repository(
                    new \Aws\S3\S3Client($this->cfg('repos.aws.s3client')),
                    $this->cfg('repos.aws.bucket'),
                    $this->cfg('prefix')
                ));
                break;
        }
    }

    private function cfg(string $key)
    {
        return config(self::CONFIG_NAME.'.'.$key);
    }

    private function getMyConfigPath()
    {
        return __DIR__.'/config/config.php';
    }
}
