<?php

namespace NickDeKruijk\Settings;

use Illuminate\Database\Eloquent\Model;

class SettingSaved
{
    public function __construct(Setting $setting)
    {
        // The setting was saved, forget the cached value so the next read
        // fetches it from the database again
        Setting::forget($setting->key);
    }

}
