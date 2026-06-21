<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    public static function createUser(array $data): int
    {
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);

        return self::create($data);
    }

    public static function documentsUsedThisMonth(int $userId): int
    {
        $stmt = self::db()->prepare(
            "SELECT COUNT(*) AS c FROM documents WHERE user_id = :user_id AND created_at >= DATE_FORMAT(NOW(), '%Y-%m-01')"
        );
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetch()['c'];
    }

    public static function setStripeCustomerId(int $userId, string $customerId): void
    {
        self::update($userId, ['stripe_customer_id' => $customerId]);
    }

    public static function applyPlan(int $userId, string $plan, ?string $billingCycle, ?string $expiresAt): void
    {
        self::update($userId, [
            'plan' => $plan,
            'plan_billing_cycle' => $billingCycle,
            'plan_expires_at' => $expiresAt,
        ]);
    }

    public static function downgradeIfExpired(array $user): array
    {
        if ($user['plan'] === 'free' || empty($user['plan_expires_at'])) {
            return $user;
        }

        if (strtotime($user['plan_expires_at']) >= time()) {
            return $user;
        }

        self::applyPlan((int) $user['id'], 'free', null, null);

        return self::find((int) $user['id']) ?? $user;
    }

    public static function paginateAll(int $page, int $perPage = 20, string $search = ''): array
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        $where = '';

        if ($search !== '') {
            $where = ' WHERE name LIKE :search OR email LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $stmt = self::db()->prepare('SELECT * FROM users' . $where . ' ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue('limit', $perPage, \PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
