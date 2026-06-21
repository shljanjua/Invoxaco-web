<?php

namespace App\Models;

use App\Core\Model;
use App\Core\Request;

class ActivityLog extends Model
{
    protected static string $table = 'activity_logs';

    public static function log(?int $userId, string $action, string $description = ''): void
    {
        self::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => Request::ip(),
        ]);
    }

    public static function recent(int $limit = 50): array
    {
        $stmt = self::db()->prepare(
            'SELECT a.*, u.name AS user_name FROM activity_logs a
             LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC LIMIT :limit'
        );
        $stmt->bindValue('limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}
