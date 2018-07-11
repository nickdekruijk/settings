<?php

namespace NickDeKruijk\Settings;

use Illuminate\Database\Eloquent\Model;

class SettingSaved
{
    public function __construct(Setting $setting)
    {
        // The setting was saved, clear the cache
        Setting::cache($setting->key, null);
    }

}
