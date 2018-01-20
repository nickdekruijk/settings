<?php

namespace LaraPages\Settings;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Setting::truncate();
        Setting::create(['key' => 'textcolor', 'value' => '#222']);
        Setting::create(['key' => 'bgcolor', 'value' => '#f0f8f4']);
    }
}
