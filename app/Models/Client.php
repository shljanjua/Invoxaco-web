<?php

namespace App\Models;

use App\Core\Model;

class Client extends Model
{
    protected static string $table = 'clients';

    public static function forUser(int $userId, string $search = ''): array
    {
        $sql = 'SELECT * FROM clients WHERE user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($search !== '') {
            $sql .= ' AND (name LIKE :search OR email LIKE :search OR company LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY name ASC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $stmt = self::db()->prepare('SELECT * FROM clients WHERE id = :id AND user_id = :user_id LIMIT 1');
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
