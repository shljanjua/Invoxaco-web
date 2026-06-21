<?php

namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected static string $table = 'payments';

    public static function recent(int $limit = 50): array
    {
        $stmt = self::db()->prepare(
            'SELECT p.*, u.name AS user_name, u.email AS user_email
             FROM payments p JOIN users u ON u.id = p.user_id
             ORDER BY p.created_at DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public static function findByGatewayPaymentId(string $gatewayPaymentId): ?array
    {
        return self::findBy('gateway_payment_id', $gatewayPaymentId);
    }

    public static function totalRevenue(): float
    {
        $stmt = self::db()->query("SELECT COALESCE(SUM(amount),0) AS total FROM payments WHERE status = 'completed'");

        return (float) $stmt->fetch()['total'];
    }
}
