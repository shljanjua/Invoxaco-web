<?php

namespace App\Models;

use App\Core\Model;

class Team extends Model
{
    protected static string $table = 'teams';

    public static function forOwner(int $ownerId): ?array
    {
        return self::findBy('owner_id', $ownerId);
    }
}
