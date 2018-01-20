<?php

namespace LaraPages\Settings;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    // On created and updated trigger SettingSaved to update cache
    protected $dispatchesEvents = [
        'created' => SettingSaved::class,
        'updated' => SettingSaved::class,
    ];

    /**
     * Set a Setting value and optional description
     *
     * @return Setting
     */
    public static function set($key, $value, $description = null)
    {
        // Find the current setting
        $setting = Setting::where('key', $key);
        if ($setting->count() == 0) {
            // It doesn't exist yet, create it
            $setting = new Setting;
            $setting->key = $key;
            $setting->value = $value;
            $setting->description = $description;
        } else {
            // Fetch the first and update value
            $setting = $setting->first();
            $setting->value = $value;
            // Update description if provided
            if ($description) {
                $setting->description = $description;
            }
        }
        // Save it to database
        $setting->save();
        // return Setting instance
        return $setting;
    }

    /**
     * Store Setting in the cache.
     *
     * @return void
     */
    public static function cache($key, $value)
    {
        $cacheKey = config('settings.cache_prefix', 'setting_').$key;
        cache([$cacheKey => $value], config('settings.cache_expires', 60));
    }

    /**
     * Get a Setting value from cache or database
     *
     * @return text
     */
    public static function get($key, $default)
    {
        // Set the cache key
        $cacheKey = config('settings.cache_prefix', 'setting_').$key;
        // Check if key exists in cache and return it
        if ($value = cache($cacheKey)) {
            return $value;
        }
        // Not in cache yet, so fetch it from model
        $setting = Setting::where('key', $key)->first();
        // Set value to actual value from model or if not found use default value
        $value = isset($setting->value) ? $setting->value : $default;
        // Store the key and value in cache
        Setting::cache($key, $value);
        // Return it
        return $value;
    }

}
