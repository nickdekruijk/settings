<?php

namespace LaraPages\Settings;

use Illuminate\Database\Eloquent\Model;

class SettingSaved
{
    public function __construct(Setting $setting)
    {
        // The setting was saved, update the cache
        Setting::cache($setting->key, $setting->value);
    }

}
