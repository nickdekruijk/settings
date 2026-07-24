[![Latest Stable Version](https://poser.pugx.org/nickdekruijk/settings/v/stable)](https://packagist.org/packages/nickdekruijk/settings)
[![Latest Unstable Version](https://poser.pugx.org/nickdekruijk/settings/v/unstable)](https://packagist.org/packages/nickdekruijk/settings)
[![Monthly Downloads](https://poser.pugx.org/nickdekruijk/settings/d/monthly)](https://packagist.org/packages/nickdekruijk/settings)
[![Total Downloads](https://poser.pugx.org/nickdekruijk/settings/downloads)](https://packagist.org/packages/nickdekruijk/settings)
[![License](https://poser.pugx.org/nickdekruijk/settings/license)](https://packagist.org/packages/nickdekruijk/settings)

# Settings
A basic cache enabled Setting model, migration and helper for your Laravel project.
It uses a database to store settings for your application. When retrieving settings they are stored in the Laravel cache to prevent unnecessary database queries.

## Installation
To install the package use

`composer require nickdekruijk/settings`

## Configuration
If you don't like the default configuration options publish the config file and change the `settings.php` file in your Laravel `app/config` folder.

`php artisan vendor:publish --tag=config --provider="NickDeKruijk\Settings\ServiceProvider"`

## Usage

### Retrieving settings
If the setting table is created (run `php artisan migrate`) and you added your first setting you can use `setting('key');` from anywhere in your application. The setting helper also accepts a default value in case the key isn't present in the database like `setting('key', 'defaultvalue');`. You can call `NickDeKruijk\Settings\Setting::get($key)` too.

### Retrieving a setting as array
When you have a setting with a value like this:
```
facebook = https://www.facebook.com/
twitter = https://twitter.com/
instagram = https://instagram.com/
```
You can have it returned as an array using
`setting('key', null, '=')`
which will return this array
```php
[
  "facebook" => "https://www.facebook.com/",
  "twitter" => "https://twitter.com/",
  "instagram" => "https://instagram.com/"
]
```


### Adding settings
To update of create a new setting you use the setting helper with an array like `setting(['key' => 'value']);` or call `NickDeKruijk\Settings\Setting::set([$key => $value]);`. To include description you can use `setting(['key' => ['value' => 1, 'description' => 'string']]);`.
The setting will be added to the database or updated if it already exists. The Setting Model also triggers an event on updated and created to store the new value in the cache.

## Leap admin module
Since 1.3.0 the package ships an admin screen for editing settings and registers it with
[Leap](https://github.com/nickdekruijk/leap) automatically when Leap is installed — the
ServiceProvider appends `NickDeKruijk\Settings\Leap\Setting::class` to
`leap.default_modules`, so there is nothing to configure. Without Leap the class is never
autoloaded and the package stays standalone.

**Upgrading from 1.2 or earlier:** a project that wrote its own `app/Leap/Setting.php`
now has that screen twice. Delete your copy to use the one the package ships. Keeping it
is also fine — from Leap 1.0.2 a module in `app/Leap/` replaces one a package registered
under the same slug, so your version simply wins and there is no duplicate. On Leap 1.0.1
and earlier there is no such precedence and the screen is listed twice, so there you have
to delete one.

## License
Settings is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
