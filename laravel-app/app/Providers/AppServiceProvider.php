<?php

namespace App\Providers;

use Alchemy\Zippy\Zippy;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('zippy', function () {
            $zippy = Zippy::load();
            // Give the PHP Zip Extension priority for zip files
            $zippy->addStrategy(
                new class($zippy->adapters) extends \Alchemy\Zippy\FileStrategy\ZipFileStrategy
                {
                    protected function getServiceNames()
                    {
                        return [
                            'Alchemy\\Zippy\\Adapter\\ZipExtensionAdapter'
                        ];
                    }
                }
            );
            return $zippy;
        });

        $this->app->alias('zippy', Zippy::class);

        $this->app->singleton(\App\DownloadsManager::class, function ($app) {
            return new \App\DownloadsManager(storage_path('downloads'), $app['zippy']);
        });

        $this->app->singleton(\App\Browser::class, function ($app) {
            return new \App\Browser($app[\App\DownloadsManager::class]);
        });
    }
}
