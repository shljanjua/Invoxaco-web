<?php

namespace App\Models;

use App\Core\Model;

class Subscription extends Model
{
    protected static string $table = 'subscriptions';

    public static function activeForUser(int $userId): ?array
    {
        $stmt = self::db()->prepare(
            "SELECT * FROM subscriptions WHERE user_id = :user_id AND status = 'active' ORDER BY created_at DESC LIMIT 1"
        );
        $stmt->execute(['user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function findByGatewaySubscriptionId(string $gatewaySubscriptionId): ?array
    {
        return self::findBy('gateway_subscription_id', $gatewaySubscriptionId);
    }

    public static function upsertForUser(int $userId, array $data): void
    {
        $stmt = self::db()->prepare(
            'SELECT * FROM subscriptions WHERE user_id = :user_id AND gateway = :gateway ORDER BY created_at DESC LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId, 'gateway' => $data['gateway']]);
        $existing = $stmt->fetch();

        $data['user_id'] = $userId;

        if ($existing) {
            self::update($existing['id'], $data);

            return;
        }

        self::create($data);
    }

    public static function allWithUser(int $limit = 100): array
    {
        $stmt = self::db()->prepare(
            'SELECT s.*, u.name AS user_name, u.email AS user_email
             FROM subscriptions s JOIN users u ON u.id = s.user_id
             ORDER BY s.created_at DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
