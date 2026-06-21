<?php

namespace App\Models;

use App\Core\Model;

class SupportTicket extends Model
{
    protected static string $table = 'support_tickets';

    public static function forUser(int $userId): array
    {
        return self::where(['user_id' => $userId], 'created_at', 'DESC');
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM support_tickets WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function allWithUser(): array
    {
        $stmt = self::db()->query(
            'SELECT t.*, u.name AS user_name, u.email AS user_email
             FROM support_tickets t JOIN users u ON u.id = t.user_id
             ORDER BY t.updated_at DESC'
        );

        return $stmt->fetchAll();
    }
}
