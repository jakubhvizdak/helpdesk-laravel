<?php

namespace App\Helpers;

use App\Models\Configuration;
use Illuminate\Support\Facades\Cache;

class ConfigHelper
{
    protected static string $cachePrefix = 'config:';

    protected static int $ttl = 3600;

    public static function get(string $key, mixed $default = null): mixed
    {
        $cacheKey = self::$cachePrefix . $key;

        return Cache::remember($cacheKey, self::$ttl, function () use ($key, $default) {
            $config = Configuration::where('key', $key)->first();
            return $config ? self::castValue($config->value) : $default;
        });
    }

    public static function set(string $key, mixed $value): bool
    {
        $config = Configuration::updateOrCreate(
            ['key' => $key],
            ['value' => is_array($value) || is_object($value) ? json_encode($value) : $value]
        );

        Cache::forget(self::$cachePrefix . $key);

        return (bool) $config;
    }

    public static function forget(?string $key = null): void
    {
        if ($key) {
            Cache::forget(self::$cachePrefix . $key);
        } else {
            $redis = Cache::store('redis')->connection();

            $keys = $redis->keys(self::$cachePrefix . '*');
            if (!empty($keys)) {
                $redis->del($keys);
            }
        }
    }

    protected static function castValue(mixed $value): mixed
    {
        if (is_numeric($value)) return $value + 0;
        if (in_array(strtolower($value), ['true', 'false'])) return strtolower($value) === 'true';
        if (self::isJson($value)) return json_decode($value, true);
        return $value;
    }

    protected static function isJson(mixed $string): bool
    {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }
}
