<?php

if (!function_exists('setting')) {
    /**
     * Get a Setting value from cache or database or Create/Update Settings when provided with an array
     *
     * @return text
     */
    function setting($key, $default = null, $keySeperator = false)
    {
        if (is_array($key)) {
            return LaraPages\Settings\Setting::set($key);
        } else {
            return LaraPages\Settings\Setting::get($key, $default, $keySeperator);
        }
    }
}
