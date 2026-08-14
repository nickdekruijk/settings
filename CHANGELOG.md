# Changelog

All notable changes to `nickdekruijk/settings` are documented here.
The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/)
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.4.0] - 2026-08-14

### Fixed
- `setting('key')` and `setting_array('key')` no longer overwrite each other in the cache.
  Both forms were stored under the same `setting_{key}` entry, so whichever ran first
  decided the return type for the rest of the request. A layout calling
  `array_filter(setting_array('nav_cta'))` then died with "must be of type array, string
  given" on every page as soon as any other code read that key as a string. Only the raw
  database value is cached now; `$default` and `$keySeperator` are applied after the cache
  read.
- A setting that is legitimately `"0"` or `""`, and a key that isn't in the database at
  all, are cached instead of hitting the database on every request. The old truthy check
  treated them as a cache miss.
- `$default` is no longer cached, so one caller's fallback can't leak into the next one's.
- `trim()` is given a string cast. It was fed `null` for a missing setting, which is
  deprecated on PHP 8.5 and an error on PHP 9.
- `SettingSaved` forgets the cache entry instead of storing `null` in it, which left an
  empty entry behind until the TTL expired.

### Added
- `Setting::forget($key)` to drop a cached setting. `Setting::cache($key, null)` still does
  the same thing.
- A test suite (PHPUnit + Orchestra Testbench). The package had none.

## [1.3.2] - 2026-07-24

### Changed
- Documented the Leap module and the upgrade step in the README.

## [1.3.1] - 2026-07-10

### Fixed
- `registerLeapModule()` is idempotent. With a cached config it appended the module to
  `leap.default_modules` on every request, listing the settings screen more than once.

## [1.3.0] - 2026-07-08

### Added
- A Leap admin module for editing settings, auto-registered when
  [Leap](https://github.com/nickdekruijk/leap) is installed. Projects with their own
  `app/Leap/Setting.php` should delete it; see the README.

## [1.2.0] - 2022-03-10

### Added
- `setting_array()` helper, shorthand for `setting($key, $default, ':')`.

## [1.1.6] - 2022-03-08

### Changed
- `Setting::get()` is documented as returning `mixed`.

## [1.1.5] - 2022-02-11

### Added
- `getAdminConfig()` for `nickdekruijk/admin` 2.0 compatibility.

## [1.1.4] - 2021-05-04

### Fixed
- The `value` column is nullable.

## [1.1.3] - 2019-11-26

### Fixed
- Splitting a value with `$keySeperator` no longer overwrites array keys with the loop
  index.
- Cache expiry uses the Laravel 5.8 argument format.

## [1.1.2] - 2019-11-19

### Fixed
- The cache is updated when a setting is deleted, not only on create and update.

## [1.1.1] - 2019-09-06

### Changed
- composer.json metadata.

## [1.1.0] - 2018-07-11

### Changed
- Package renamed to `nickdekruijk/settings`.

## [1.0.4] - 2018-06-21

### Added
- Optional `$keySeperator` argument on `setting()` to return a value as an array by
  splitting it into lines and key/value pairs.

## [1.0.2] - 2018-02-16

### Changed
- The `value` column is a `longText` instead of a `string`.

## [1.0.0] - 2018-01-20

### Added
- First release: `Setting` model, migration, `setting()` helper and cache handling.

[1.4.0]: https://github.com/nickdekruijk/settings/compare/1.3.2...1.4.0
[1.3.2]: https://github.com/nickdekruijk/settings/compare/1.3.1...1.3.2
[1.3.1]: https://github.com/nickdekruijk/settings/compare/1.3.0...1.3.1
[1.3.0]: https://github.com/nickdekruijk/settings/compare/1.2.0...1.3.0
[1.2.0]: https://github.com/nickdekruijk/settings/compare/1.1.6...1.2.0
[1.1.6]: https://github.com/nickdekruijk/settings/compare/1.1.5...1.1.6
[1.1.5]: https://github.com/nickdekruijk/settings/compare/1.1.4...1.1.5
[1.1.4]: https://github.com/nickdekruijk/settings/compare/1.1.3...1.1.4
[1.1.3]: https://github.com/nickdekruijk/settings/compare/1.1.2...1.1.3
[1.1.2]: https://github.com/nickdekruijk/settings/compare/1.1.1...1.1.2
[1.1.1]: https://github.com/nickdekruijk/settings/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/nickdekruijk/settings/compare/1.0.4...1.1.0
[1.0.4]: https://github.com/nickdekruijk/settings/compare/1.0.2...1.0.4
[1.0.2]: https://github.com/nickdekruijk/settings/compare/1.0.0...1.0.2
[1.0.0]: https://github.com/nickdekruijk/settings/releases/tag/1.0.0
