[![Latest Stable Version](https://poser.pugx.org/larapages/settings/v/stable)](https://packagist.org/packages/larapages/settings)
[![Latest Unstable Version](https://poser.pugx.org/larapages/settings/v/unstable)](https://packagist.org/packages/larapages/settings)
[![Monthly Downloads](https://poser.pugx.org/larapages/settings/d/monthly)](https://packagist.org/packages/larapages/settings)
[![Total Downloads](https://poser.pugx.org/larapages/settings/downloads)](https://packagist.org/packages/larapages/settings)
[![License](https://poser.pugx.org/larapages/settings/license)](https://packagist.org/packages/larapages/settings)

# LaraPages/Settings
A basic cache enabled Setting model, migration and helper for your Laravel project.
It uses a database to store settings for your application. When retrieving settings they are stored in the Laravel cache to prevent unnecessary database queries.

## Installation
To install the package use

`composer require larapages/settings`

## Configuration
If you don't like the default configuration options publish the config file and change the `settings.php` file in your Laravel `app/config` folder.

`php artisan vendor:publish --tag=config --provider="LaraPages\Settings\ServiceProvider"` 

## Usage

### Retrieving settings
If the setting table is created (run `php artisan migrate`) and you added your first setting you can use `setting('key');` from anywhere in your application. The setting helper also accepts a default value in case the key isn't present in the database like `setting('key', 'defaultvalue');`. You can call `LaraPages\Settings\Setting::get($key)` too.

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
To update of create a new setting you use the setting helper with an array like `setting(['key' => 'value']);` or call `LaraPages\Settings\Setting::set([$key => $value]);`. To include description you can use `setting(['key' => ['value' => 1, 'description' => 'string']]);`.
The setting will be added to the database or updated if it already exists. The Setting Model also triggers an event on updated and created to store the new value in the cache.

## License
LaraPages is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
