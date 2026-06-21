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

    public static function monthlySeries(int $userId, int $months = 6): array
    {
        $start = date('Y-m-01', strtotime('-' . ($months - 1) . ' months'));

        $stmt = self::db()->prepare(
            "SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym,
                    COUNT(*) AS doc_count,
                    SUM(CAST(JSON_UNQUOTE(JSON_EXTRACT(data, '$.grand_total')) AS DECIMAL(14,2))) AS total_value
             FROM documents
             WHERE user_id = :user_id AND created_at >= :start
             GROUP BY ym ORDER BY ym ASC"
        );
        $stmt->execute(['user_id' => $userId, 'start' => $start]);
        $rows = $stmt->fetchAll();
        $byMonth = [];
        foreach ($rows as $row) {
            $byMonth[$row['ym']] = $row;
        }

        $series = [];
        for ($i = $months - 1; $i >= 0; $i--) {
            $ym = date('Y-m', strtotime('-' . $i . ' months'));
            $series[] = [
                'label' => date('M Y', strtotime($ym . '-01')),
                'count' => (int) ($byMonth[$ym]['doc_count'] ?? 0),
                'value' => (float) ($byMonth[$ym]['total_value'] ?? 0),
            ];
        }

        return $series;
    }

    public static function statusBreakdown(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT status, COUNT(*) AS c FROM documents WHERE user_id = :user_id GROUP BY status'
        );
        $stmt->execute(['user_id' => $userId]);
        $rows = $stmt->fetchAll();

        $breakdown = ['draft' => 0, 'final' => 0];
        foreach ($rows as $row) {
            $breakdown[$row['status']] = (int) $row['c'];
        }

        return $breakdown;
    }

    public static function countInMonth(int $userId, int $monthsAgo = 0): int
    {
        $start = date('Y-m-01', strtotime('-' . $monthsAgo . ' months'));
        $end = date('Y-m-01', strtotime('-' . ($monthsAgo - 1) . ' months'));

        $stmt = self::db()->prepare(
            'SELECT COUNT(*) AS c FROM documents WHERE user_id = :user_id AND created_at >= :start AND created_at < :end'
        );
        $stmt->execute(['user_id' => $userId, 'start' => $start, 'end' => $end]);

        return (int) $stmt->fetch()['c'];
    }
}
