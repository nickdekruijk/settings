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
            __DIR__ . '/config.php' => config_path('settings.php'),
        ], 'config');
        if (config('settings.migration')) {
            $this->loadMigrationsFrom(__DIR__ . '/migrations/');
        }
        $this->registerHelpers();
        $this->registerLeapModule();
    }

    /**
     * Register the settings admin module with Leap when Leap is installed.
     * Guarded on class_exists so the settings package stays standalone; Leap
     * reads leap.default_modules at request time (ModuleController::getAllModules).
     *
     * @return void
     */
    public function registerLeapModule()
    {
        if (class_exists(\NickDeKruijk\Leap\Module::class)) {
            config(['leap.default_modules' => array_merge(
                config('leap.default_modules', []),
                [\NickDeKruijk\Settings\Leap\Setting::class],
            )]);
        }
    }

    /**
     * Register helpers file
     */
    public function registerHelpers()
    {
        if (file_exists($file = __DIR__ . '/helpers.php')) {
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
        $this->mergeConfigFrom(__DIR__ . '/config.php', 'settings');
    }
}
