<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class DigitalProduct extends Model
{
    protected static string $table = 'digital_products';

    public static function findBySlug(string $slug): ?array
    {
        return self::findBy('slug', $slug);
    }

    /** Active products for the public storefront, optionally filtered by category. */
    public static function published(?int $categoryId = null, ?string $type = null): array
    {
        $sql = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
                FROM digital_products p
                LEFT JOIN store_categories c ON c.id = p.category_id
                WHERE p.is_active = 1';
        $params = [];

        if ($categoryId !== null) {
            $sql .= ' AND p.category_id = :cid';
            $params['cid'] = $categoryId;
        }
        if ($type !== null && $type !== '') {
            $sql .= ' AND p.type = :type';
            $params['type'] = $type;
        }

        $sql .= ' ORDER BY p.is_featured DESC, p.sort_order ASC, p.created_at DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function featured(int $limit = 6): array
    {
        $stmt = self::db()->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM digital_products p
             LEFT JOIN store_categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND p.is_featured = 1
             ORDER BY p.sort_order ASC LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function allForAdmin(): array
    {
        $stmt = self::db()->query(
            'SELECT p.*, c.name AS category_name
             FROM digital_products p
             LEFT JOIN store_categories c ON c.id = p.category_id
             ORDER BY p.sort_order ASC, p.created_at DESC'
        );

        return $stmt->fetchAll();
    }

    public static function withCategory(int $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug
             FROM digital_products p
             LEFT JOIN store_categories c ON c.id = p.category_id
             WHERE p.id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);

        return $stmt->fetch() ?: null;
    }

    public static function incrementDownloads(int $id): void
    {
        self::db()->prepare('UPDATE digital_products SET downloads_count = downloads_count + 1 WHERE id = :id')
            ->execute(['id' => $id]);
    }

    /** The effective price a customer pays right now (sale price if set and lower). */
    public static function effectivePrice(array $product): float
    {
        $price = (float) $product['price'];
        $sale = $product['sale_price'] !== null ? (float) $product['sale_price'] : null;

        if ($sale !== null && $sale >= 0 && $sale < $price) {
            return $sale;
        }

        return $price;
    }

    public static function isOnSale(array $product): bool
    {
        return $product['sale_price'] !== null
            && (float) $product['sale_price'] >= 0
            && (float) $product['sale_price'] < (float) $product['price'];
    }
}
