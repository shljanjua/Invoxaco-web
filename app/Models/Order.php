<?php

namespace App\Models;

use App\Core\Model;
use PDO;

class Order extends Model
{
    protected static string $table = 'orders';

    public static function findByToken(string $token): ?array
    {
        return self::findBy('token', $token);
    }

    public static function findByGatewayPaymentId(string $id): ?array
    {
        return self::findBy('gateway_payment_id', $id);
    }

    public static function items(int $orderId): array
    {
        $stmt = self::db()->prepare(
            'SELECT oi.*, p.slug AS product_slug
             FROM order_items oi
             LEFT JOIN digital_products p ON p.id = oi.product_id
             WHERE oi.order_id = :id ORDER BY oi.id ASC'
        );
        $stmt->execute(['id' => $orderId]);

        return $stmt->fetchAll();
    }

    public static function recent(int $limit = 100): array
    {
        $stmt = self::db()->prepare(
            'SELECT o.*,
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
             FROM orders o ORDER BY o.created_at DESC LIMIT :lim'
        );
        $stmt->bindValue('lim', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function forUser(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT o.*,
                    (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS item_count
             FROM orders o WHERE o.user_id = :uid ORDER BY o.created_at DESC'
        );
        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll();
    }

    public static function storeRevenue(): float
    {
        $stmt = self::db()->query("SELECT COALESCE(SUM(total),0) AS t FROM orders WHERE status IN ('paid','free')");

        return (float) $stmt->fetch()['t'];
    }
}
