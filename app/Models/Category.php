<?php

namespace App\Models;

use App\Core\Model;

class Category extends Model
{
    protected static string $table = 'categories';

    public static function withTemplateCounts(): array
    {
        $sql = "SELECT c.*, COUNT(t.id) AS template_count
                FROM categories c
                LEFT JOIN document_templates t ON t.category_id = c.id AND t.is_active = 1
                GROUP BY c.id
                ORDER BY c.sort_order ASC";

        return self::db()->query($sql)->fetchAll();
    }
}
