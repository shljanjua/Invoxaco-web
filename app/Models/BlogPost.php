<?php

namespace App\Models;

use App\Core\Model;

class BlogPost extends Model
{
    protected static string $table = 'blog_posts';

    public static function published(int $limit = 0, int $offset = 0): array
    {
        $sql = "SELECT b.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name
                FROM blog_posts b
                LEFT JOIN blog_categories c ON c.id = b.category_id
                LEFT JOIN users u ON u.id = b.author_id
                WHERE b.status = 'published' AND b.published_at <= NOW()
                ORDER BY b.published_at DESC";

        if ($limit > 0) {
            $sql .= ' LIMIT ' . (int) $limit . ' OFFSET ' . (int) $offset;
        }

        return self::db()->query($sql)->fetchAll();
    }

    public static function findPublishedBySlug(string $slug): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT b.*, c.name AS category_name, c.slug AS category_slug, u.name AS author_name
             FROM blog_posts b
             LEFT JOIN blog_categories c ON c.id = b.category_id
             LEFT JOIN users u ON u.id = b.author_id
             WHERE b.slug = :slug AND b.status = 'published' LIMIT 1"
        );
        $stmt->execute(['slug' => $slug]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function countPublished(): int
    {
        return (int) self::db()->query("SELECT COUNT(*) AS c FROM blog_posts WHERE status = 'published'")->fetch()['c'];
    }

    public static function allWithCategory(): array
    {
        $stmt = self::db()->query(
            'SELECT b.*, c.name AS category_name, u.name AS author_name
             FROM blog_posts b
             LEFT JOIN blog_categories c ON c.id = b.category_id
             LEFT JOIN users u ON u.id = b.author_id
             ORDER BY b.created_at DESC'
        );

        return $stmt->fetchAll();
    }
}
