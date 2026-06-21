<?php

namespace App\Models;

use App\Core\Model;

class SmtpSetting extends Model
{
    protected static string $table = 'smtp_settings';

    public static function active(): ?array
    {
        $stmt = self::db()->query('SELECT * FROM smtp_settings WHERE is_active = 1 ORDER BY id DESC LIMIT 1');
        $row = $stmt->fetch();

        return $row ?: null;
    }
}
