<?php

namespace NickDeKruijk\Settings;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/config.php' => config_path('settings.php'),
        ], 'config');
        if (config('settings.migration')) {
            $this->loadMigrationsFrom(__DIR__.'/migrations/');
        }
        $this->registerHelpers();
    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        if (file_exists($file = __DIR__.'/helpers.php')) {
            require $file;
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config.php', 'settings');
    }
}
