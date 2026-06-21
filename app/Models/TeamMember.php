<?php

namespace App\Models;

use App\Core\Model;

class TeamMember extends Model
{
    protected static string $table = 'team_members';

    public static function forTeam(int $teamId): array
    {
        return self::where(['team_id' => $teamId], 'created_at', 'ASC');
    }

    public static function findByToken(string $token): ?array
    {
        return self::findBy('invite_token', $token);
    }
}
