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
If the setting table is created (run `php artisan migrate`) you can use `setting('key');` from anywhere in your application. The setting helper also accepts a default value in case the key isn't present in the database like `setting('key', 'defaultvalue');`.

## License
LaraPages is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).