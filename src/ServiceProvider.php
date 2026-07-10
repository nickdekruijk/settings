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
    }

    /**
     * Register the settings admin module with Leap when Leap is installed.
     * Guarded on class_exists so the settings package stays standalone; Leap
     * reads leap.default_modules at request time (ModuleController::getAllModules).
     *
     * Idempotent: skips re-adding when already present, since this booting()
     * callback runs on every request and config('leap.default_modules') may
     * already contain it -- e.g. a cached config (php artisan config:cache)
     * bakes in whatever was in the array at cache time, so an unconditional
     * array_merge would append a duplicate on every request thereafter, and a
     * repeated config:cache without an intervening config:clear compounds it
     * further (2x, 3x, ...).
     *
     * @return void
     */
    public function registerLeapModule()
    {
        if (class_exists(\NickDeKruijk\Leap\Module::class)) {
            $modules = config('leap.default_modules', []);
            if (! in_array(\NickDeKruijk\Settings\Leap\Setting::class, $modules, true)) {
                $modules[] = \NickDeKruijk\Settings\Leap\Setting::class;
                config(['leap.default_modules' => $modules]);
            }
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

        // Register the Leap module during the booting phase — this runs before any
        // provider's boot(), so it is in place before Leap registers its module
        // routes (leap.module.settings) in its own boot().
        $this->app->booting(fn () => $this->registerLeapModule());
    }
}
