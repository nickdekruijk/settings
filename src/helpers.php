<?php

if (!function_exists('setting')) {
    /**
     * Get a Setting value from cache or database or Create/Update Settings when provided with an array
     *
     * @return mixed
     */
    function setting($key, $default = null, $keySeperator = false)
    {
        if (is_array($key)) {
            return NickDeKruijk\Settings\Setting::set($key);
        } else {
            return NickDeKruijk\Settings\Setting::get($key, $default, $keySeperator);
        }
    }
}

if (!function_exists('setting_array')) {
    /**
     * Get a Setting array from cache or database
     *
     * @return array
     */
    function setting_array($key, $default = null, $keySeperator = ':')
    {
        return NickDeKruijk\Settings\Setting::get($key, $default, $keySeperator);
    }
}
