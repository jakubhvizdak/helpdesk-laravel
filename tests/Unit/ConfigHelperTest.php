<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Helpers\ConfigHelper;
use App\Models\Configuration;
use Illuminate\Support\Facades\Cache;
use Mockery;

class ConfigHelperTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_get_returns_default_when_no_config()
    {
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn('default_value');

        $value = ConfigHelper::get('nonexistent', 'default_value');

        $this->assertEquals('default_value', $value);
    }

    public function test_get_returns_config_from_cache()
    {
        $configValue = 'cached_value';

        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($configValue);

        $value = ConfigHelper::get('some_key');

        $this->assertEquals($configValue, $value);
    }

    public function test_set_stores_value_and_forgets_cache()
    {
        $mock = Mockery::mock('alias:' . Configuration::class);
        $mock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                ['key' => 'test_key'],
                ['value' => 123]
            )
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->once()
            ->with('config:test_key');

        $result = ConfigHelper::set('test_key', 123);

        $this->assertTrue($result);
    }

    public function test_set_json_encodes_arrays_and_objects()
    {
        $mock = Mockery::mock('alias:' . Configuration::class);
        $mock->shouldReceive('updateOrCreate')
            ->once()
            ->with(
                ['key' => 'json_key'],
                ['value' => json_encode(['a' => 1])]
            )
            ->andReturn(true);

        Cache::shouldReceive('forget')
            ->once()
            ->with('config:json_key');

        $result = ConfigHelper::set('json_key', ['a' => 1]);

        $this->assertTrue($result);
    }

    public function test_forget_specific_key()
    {
        Cache::shouldReceive('forget')
            ->once()
            ->with('config:specific_key');

        ConfigHelper::forget('specific_key');
    }

    public function test_cast_value_numeric_and_boolean_and_json()
    {
        $method = new \ReflectionMethod(ConfigHelper::class, 'castValue');
        $method->setAccessible(true);

        $this->assertSame(123, $method->invoke(null, '123'));
        $this->assertSame(123.45, $method->invoke(null, '123.45'));
        $this->assertSame(true, $method->invoke(null, 'true'));
        $this->assertSame(false, $method->invoke(null, 'false'));
        $this->assertSame(['a' => 1], $method->invoke(null, json_encode(['a' => 1])));
    }

    public function test_cast_value_returns_string_for_non_json_non_numeric()
    {
        $method = new \ReflectionMethod(ConfigHelper::class, 'castValue');
        $method->setAccessible(true);

        $this->assertSame('hello', $method->invoke(null, 'hello'));
    }

    public function test_isJson_detects_json_correctly()
    {
        $method = new \ReflectionMethod(ConfigHelper::class, 'isJson');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke(null, json_encode(['x' => 1])));
        $this->assertFalse($method->invoke(null, 'not json'));
        $this->assertFalse($method->invoke(null, 123));
    }
}
