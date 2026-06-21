<?php

namespace App\Models;

use App\Core\Model;

class SeoSetting extends Model
{
    protected static string $table = 'seo_settings';

    public static function forPage(string $pageKey): ?array
    {
        return self::findBy('page_key', $pageKey);
    }

    public static function upsert(string $pageKey, array $data): void
    {
        $existing = self::forPage($pageKey);

        if ($existing) {
            self::update($existing['id'], $data);

            return;
        }

        $data['page_key'] = $pageKey;
        self::create($data);
    }
}
