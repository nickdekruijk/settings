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
     * Set multiple Setting from an array
     *
     * @return Setting
     */
    public static function set(Array $keys)
    {
        foreach($keys as $key => $value) {
            if (is_array($value)) {
                Setting::set_save($key, $value['value'], $value['description']);
            } else {
                Setting::set_save($key, $value);
            }
        }
    }

    /**
     * Set a Setting value and optional description
     *
     * @return Setting
     */
    private static function set_save($key, $value, $description = null)
    {
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
            if ($description) {
                $setting->description = $description;
            }
        }
        $setting->save();
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
    public static function get($key, $default = null, $keySeperator = false)
    {
        $cacheKey = config('settings.cache_prefix', 'setting_').$key;

        // Check if key exists in cache and return it
        if ($value = cache($cacheKey)) {
            return $value;
        }

        // Not in cache yet, so fetch it from model
        $setting = Setting::where('key', $key)->first();

        // Set value to actual value from model or if not found use default value
        $value = $setting->value ?? $default;

        // If $keySeperator parameter is set return an array by splitting value into seperate lines and key values
        if ($keySeperator) {
            $array = [];
            foreach(array_map('trim', explode(chr(10), trim($value))) as $key => $val) {
                $line = array_map('trim', explode($keySeperator, $val, 2));
                if (isset($line[1])) {
                    $array[$line[0]] = $line[1];
                } else {
                    $array[] = $line[0];
                }
            }
            $value = $array;
        }
        dd($value);
        Setting::cache($key, $value);
        return $value;
    }

}
