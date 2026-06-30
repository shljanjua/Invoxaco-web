<?php

namespace App\Models;

use App\Core\Model;

class StoreCategory extends Model
{
    protected static string $table = 'store_categories';

    public static function ordered(): array
    {
        return self::all('sort_order', 'ASC');
    }

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }
}
