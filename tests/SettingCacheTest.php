<?php

namespace NickDeKruijk\Settings\Tests;

use Illuminate\Support\Facades\DB;
use NickDeKruijk\Settings\Setting;

class SettingCacheTest extends TestCase
{
    /**
     * The raw value and the array form must not overwrite each other in the
     * cache, whichever one is read first.
     */
    public function test_string_first_then_array()
    {
        Setting::set(['x' => "a:1\nb:2"]);

        $this->assertIsString(setting('x'));
        $this->assertIsArray(setting_array('x'));
        $this->assertSame(['a' => '1', 'b' => '2'], setting_array('x'));
    }

    public function test_array_first_then_string()
    {
        Setting::set(['x' => "a:1\nb:2"]);

        $this->assertIsArray(setting_array('x'));
        $this->assertIsString(setting('x'));
        $this->assertSame("a:1\nb:2", setting('x'));
    }

    /**
     * A different seperator on the same key must split on that seperator, not
     * return whatever a previous call happened to cache.
     */
    public function test_different_seperators_on_the_same_key()
    {
        Setting::set(['x' => 'a=1']);

        $this->assertSame(['a=1'], setting('x', null, ':'));
        $this->assertSame(['a' => '1'], setting('x', null, '='));
    }

    /**
     * Saving a setting must invalidate every form of the cached value.
     */
    public function test_saving_clears_both_forms()
    {
        Setting::set(['x' => "a:1\nb:2"]);
        setting('x');
        setting_array('x');

        Setting::set(['x' => 'c:3']);

        $this->assertSame(['c' => '3'], setting_array('x'));
        $this->assertStringContainsString('c:3', setting('x'));
    }

    /**
     * A falsy value is a real value, not a cache miss.
     */
    public function test_falsy_values_are_cached()
    {
        Setting::set(['zero' => '0', 'empty' => '']);

        setting('zero');
        setting('empty');

        DB::enableQueryLog();
        $this->assertSame('0', setting('zero'));
        $this->assertSame('', setting('empty'));
        $this->assertCount(0, DB::getQueryLog());
    }

    /**
     * A missing setting is cached too, so a default doesn't cost a query on
     * every request.
     */
    public function test_missing_setting_is_cached()
    {
        $this->assertSame('fallback', setting('nope', 'fallback'));

        DB::enableQueryLog();
        $this->assertSame('fallback', setting('nope', 'fallback'));
        $this->assertCount(0, DB::getQueryLog());
    }

    /**
     * The default of one caller must not leak into the next one.
     */
    public function test_default_is_not_cached()
    {
        $this->assertSame('one', setting('nope', 'one'));
        $this->assertSame('two', setting('nope', 'two'));
        $this->assertNull(setting('nope'));
    }

    /**
     * Setting::cache() with a null value has always meant "invalidate", so it
     * must keep meaning that.
     */
    public function test_caching_null_invalidates()
    {
        Setting::set(['x' => 'one']);
        setting('x');

        DB::table('settings')->where('key', 'x')->update(['value' => 'two']);
        Setting::cache('x', null);

        $this->assertSame('two', setting('x'));
    }

    /**
     * Splitting a missing setting must not pass null to trim(), which is
     * deprecated on PHP 8.5.
     */
    public function test_array_form_of_a_missing_setting()
    {
        $this->assertSame([''], setting_array('nope'));
    }
}
