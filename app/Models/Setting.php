<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected static string $table = 'settings';

    public static function get(string $key, mixed $default = null): mixed
    {
        static $cache = null;

        if ($cache === null) {
            try {
                $cache = [];
                foreach (self::all() as $row) {
                    $cache[$row['key_name']] = $row['value'];
                }
            } catch (\Throwable) {
                return $default;
            }
        }

        return $cache[$key] ?? $default;
    }

    public static function set(string $key, string $value): void
    {
        $existing = self::findBy('key_name', $key);

        if ($existing) {
            self::update($existing['id'], ['value' => $value]);

            return;
        }

        self::create(['key_name' => $key, 'value' => $value]);
    }
}
