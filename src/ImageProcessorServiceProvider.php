<?php

namespace Elanode\ImageProcessor;

use Illuminate\Support\ServiceProvider;

class ImageProcessorServiceProvider extends ServiceProvider
{
    /**
     * @inheritDoc
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'imagesaver');

        $this->app->bind('imagesaver', function ($app) {
            return new ImageSaver();
        });

        $this->app->bind('imagemaker', function ($app) {
            return new ImageMaker();
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('imagesaver.php'),
            ], 'config');
        }
    }

    /**
     * @inheritDoc
     */
    public function boot()
    {
        //
    }
}
