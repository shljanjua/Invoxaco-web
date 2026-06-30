<?php

namespace App\Models;

use App\Core\Model;

class DownloadGrant extends Model
{
    protected static string $table = 'download_grants';

    public static function findByToken(string $token): ?array
    {
        return self::findBy('token', $token);
    }

    /** All grants for an order, joined to the live product for display/download. */
    public static function forOrder(int $orderId): array
    {
        $stmt = self::db()->prepare(
            'SELECT g.*, p.name AS product_name, p.slug AS product_slug, p.type AS product_type,
                    p.cover_image, p.file_name, p.file_path
             FROM download_grants g
             LEFT JOIN digital_products p ON p.id = g.product_id
             WHERE g.order_id = :id ORDER BY g.id ASC'
        );
        $stmt->execute(['id' => $orderId]);

        return $stmt->fetchAll();
    }

    /** Library view: all paid grants belonging to a user, newest first. */
    public static function libraryForUser(int $userId): array
    {
        $stmt = self::db()->prepare(
            'SELECT g.*, p.name AS product_name, p.slug AS product_slug, p.type AS product_type,
                    p.cover_image, p.file_name, o.created_at AS purchased_at, o.token AS order_token
             FROM download_grants g
             JOIN orders o ON o.id = g.order_id
             LEFT JOIN digital_products p ON p.id = g.product_id
             WHERE g.user_id = :uid AND o.status IN (\'paid\',\'free\')
             ORDER BY o.created_at DESC, g.id ASC'
        );
        $stmt->execute(['uid' => $userId]);

        return $stmt->fetchAll();
    }

    public static function incrementCount(int $id): void
    {
        self::db()->prepare('UPDATE download_grants SET download_count = download_count + 1 WHERE id = :id')
            ->execute(['id' => $id]);
    }

    /** Attach any guest grants made with this email to a user account (e.g. on register/login). */
    public static function claimForUser(string $email, int $userId): void
    {
        self::db()->prepare('UPDATE download_grants SET user_id = :uid WHERE email = :email AND user_id IS NULL')
            ->execute(['uid' => $userId, 'email' => $email]);
    }
}
