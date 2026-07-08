<?php

namespace NickDeKruijk\Settings\Leap;

use NickDeKruijk\Leap\Classes\Attribute;
use NickDeKruijk\Leap\Resource;

/**
 * Leap admin module for editing settings. Shipped by the settings package and
 * registered with Leap automatically when both packages are installed (see the
 * settings ServiceProvider). This file is only autoloaded when Leap is present,
 * so it never fatals in a project without Leap.
 */
class Setting extends Resource
{
    public $model = \NickDeKruijk\Settings\Setting::class;

    public $priority = 100;

    public $icon = 'fas-gears';

    public $title = [
        'nl' => 'Instellingen',
        'en' => 'Settings',
    ];

    public $orderBy = 'key';

    public $showIndexGroups = false;

    public function attributes()
    {
        return [
            Attribute::make('key')->index(1)->unique()->searchable()->required()->label('Setting'),
            Attribute::make('value')->index(2)->textarea()->label('Value'),
            Attribute::make('description')->index(3)->label('Description'),
        ];
    }
}
