<?php

if (!function_exists('setting')) {
    /**
     * Get a Setting value from cache or database
     *
     * @return text
     */
    function setting($key, $default = null)
    {
        return \LaraPages\Settings\Setting::get($key, $default);
    }
}

if (!function_exists('setting_set')) {
    /**
     * Set a Setting value and optional description
     *
     * @return Setting
     */
    function setting_set($key, $value, $description = null)
    {
        return \LaraPages\Settings\Setting::set($key, $value, $description);
    }
}
