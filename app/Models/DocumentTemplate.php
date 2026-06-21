<?php

namespace App\Models;

use App\Core\Model;

class DocumentTemplate extends Model
{
    protected static string $table = 'document_templates';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    public static function builtTemplates(): array
    {
        return self::where(['is_built' => 1, 'is_active' => 1], 'name', 'ASC');
    }

    public static function byCategory(int $categoryId): array
    {
        return self::where(['category_id' => $categoryId, 'is_active' => 1], 'sort_order', 'ASC');
    }

    public static function search(string $term): array
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM document_templates WHERE is_active = 1 AND name LIKE :term ORDER BY is_built DESC, name ASC LIMIT 30'
        );
        $stmt->execute(['term' => '%' . $term . '%']);

        return $stmt->fetchAll();
    }

    public static function decodeFields(array $template): array
    {
        if (empty($template['fields_schema'])) {
            return [];
        }

        $decoded = json_decode($template['fields_schema'], true);

        return $decoded['fields'] ?? [];
    }

    public static function decodeFaqs(array $template): array
    {
        if (empty($template['faqs'])) {
            return [];
        }

        return json_decode($template['faqs'], true) ?: [];
    }

    public static function allWithCategory(): array
    {
        $stmt = self::db()->query(
            'SELECT t.*, c.name AS category_name
             FROM document_templates t JOIN categories c ON c.id = t.category_id
             ORDER BY t.created_at DESC'
        );

        return $stmt->fetchAll();
    }
}
