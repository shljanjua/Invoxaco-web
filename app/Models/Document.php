<?php

namespace App\Models;

use App\Core\Model;

class Document extends Model
{
    protected static string $table = 'documents';

    public static function forUser(int $userId, string $search = ''): array
    {
        $sql = 'SELECT d.*, t.name AS template_name, t.slug AS template_slug
                FROM documents d
                JOIN document_templates t ON t.id = d.template_id
                WHERE d.user_id = :user_id';
        $params = ['user_id' => $userId];

        if ($search !== '') {
            $sql .= ' AND d.title LIKE :search';
            $params['search'] = '%' . $search . '%';
        }

        $sql .= ' ORDER BY d.updated_at DESC';

        $stmt = self::db()->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT d.*, t.name AS template_name, t.slug AS template_slug, t.fields_schema, t.plan_required
             FROM documents d JOIN document_templates t ON t.id = d.template_id
             WHERE d.id = :id AND d.user_id = :user_id LIMIT 1'
        );
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function findByShareToken(string $token): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT d.*, t.name AS template_name, t.slug AS template_slug
             FROM documents d JOIN document_templates t ON t.id = d.template_id
             WHERE d.share_token = :token LIMIT 1'
        );
        $stmt->execute(['token' => $token]);
        $row = $stmt->fetch();

        return $row ?: null;
    }

    public static function forClient(int $clientId, int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT d.*, t.name AS template_name FROM documents d
             JOIN document_templates t ON t.id = d.template_id
             WHERE d.client_id = :client_id AND d.user_id = :user_id ORDER BY d.created_at DESC'
        );
        $stmt->execute(['client_id' => $clientId, 'user_id' => $userId]);

        return $stmt->fetchAll();
    }
}
