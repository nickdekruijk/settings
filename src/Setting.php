<?php

namespace NickDeKruijk\Settings;

use Illuminate\Database\Eloquent\Model;
use NickDeKruijk\Admin\Classes\AdminConfig;

class Setting extends Model
{
    // On created and updated trigger SettingSaved to update cache
    protected $dispatchesEvents = [
        'created' => SettingSaved::class,
        'updated' => SettingSaved::class,
        'deleted' => SettingSaved::class,
    ];

    /**
     * This method allows the model to be managed with the nickdekruijk/admin 2.0 package.
     *
     * @return AdminConfig
     */
    public function getAdminConfig()
    {
        return new AdminConfig([
            'slug' => 'settings',
            'icon' => 'fa-solid fa-gears',
            'component' => 'admin.crud',
            'title' => 'Settings',
        ]);
    }

    /**
     * Set multiple Setting from an array
     *
     * @return Setting
     */
    public static function set(array $keys)
    {
        foreach ($keys as $key => $value) {
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
     * Build the cache key for a setting key.
     *
     * @return string
     */
    private static function cacheKey($key)
    {
        return config('settings.cache_prefix', 'setting_') . $key;
    }

    /**
     * Store a raw Setting value in the cache, or forget it when $value is null.
     *
     * Passing null has always been the way to invalidate a setting, so it keeps
     * doing that. Use forget() directly if you want to be explicit about it.
     *
     * @return void
     */
    public static function cache($key, $value)
    {
        if ($value === null) {
            self::forget($key);
            return;
        }
        self::store($key, $value);
    }

    /**
     * Store a raw Setting value in the cache, null included.
     *
     * The value is wrapped in an array so a legitimately falsy setting ("0", "",
     * null for a setting that doesn't exist) is still a cache hit instead of
     * hitting the database on every request.
     *
     * @return void
     */
    private static function store($key, $value)
    {
        cache([self::cacheKey($key) => ['value' => $value]], now()->addMinutes(config('settings.cache_expires', 60)));
    }

    /**
     * Remove a Setting from the cache so the next read hits the database again.
     *
     * @return void
     */
    public static function forget($key)
    {
        cache()->forget(self::cacheKey($key));
    }

    /**
     * Split a raw setting value into seperate lines and key values.
     *
     * @return array
     */
    private static function explodeValue($value, $keySeperator)
    {
        $array = [];
        foreach (array_map('trim', explode(chr(10), trim((string) $value))) as $val) {
            $line = array_map('trim', explode($keySeperator, $val, 2));
            if (isset($line[1])) {
                $array[$line[0]] = $line[1];
            } else {
                $array[] = $line[0];
            }
        }
        return $array;
    }

    /**
     * Get a Setting value from cache or database
     *
     * Only the raw database value is cached, never the array form. Both
     * setting('x') and setting_array('x') read the same entry and each applies
     * its own $default and $keySeperator afterwards, so neither can hand the
     * other the wrong type.
     *
     * @return mixed
     */
    public static function get($key, $default = null, $keySeperator = false)
    {
        $cached = cache(self::cacheKey($key));

        if (is_array($cached) && array_key_exists('value', $cached)) {
            // Cache hit, use the raw value as stored
            $value = $cached['value'];
        } else {
            // Not in cache yet, so fetch it from model and cache the raw value
            $setting = Setting::where('key', $key)->first();
            $value = $setting->value ?? null;
            self::store($key, $value);
        }

        // Use the default value if the setting isn't present in the database
        $value = $value ?? $default;

        // If $keySeperator parameter is set return an array instead of the raw value
        if ($keySeperator) {
            return self::explodeValue($value, $keySeperator);
        }

        return $value;
    }
}
