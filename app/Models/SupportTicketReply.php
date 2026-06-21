<?php

namespace App\Models;

use App\Core\Model;

class SupportTicketReply extends Model
{
    protected static string $table = 'support_ticket_replies';

    public static function forTicket(int $ticketId): array
    {
        return self::where(['ticket_id' => $ticketId], 'created_at', 'ASC');
    }
}
