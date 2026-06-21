<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;

class SupportController extends Controller
{
    public function index(): void
    {
        $this->view('admin/support-tickets/index', [
            'pageTitle' => 'Support Tickets',
            'tickets' => SupportTicket::allWithUser(),
        ], 'layouts/admin');
    }

    public function show(int $id): void
    {
        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            Response::abort(404, 'Ticket not found');
        }

        $this->view('admin/support-tickets/show', [
            'pageTitle' => 'Ticket #' . $id,
            'ticket' => $ticket,
            'replies' => SupportTicketReply::forTicket($id),
        ], 'layouts/admin');
    }

    public function reply(int $id): void
    {
        $this->validateCsrf();

        $ticket = SupportTicket::find($id);

        if (!$ticket) {
            Response::abort(404, 'Ticket not found');
        }

        $message = Request::string('message');

        if ($message !== '') {
            SupportTicketReply::create([
                'ticket_id' => $id,
                'user_id' => null,
                'is_admin' => 1,
                'message' => $message,
            ]);

            SupportTicket::update($id, ['status' => 'pending']);
        }

        $this->flashAndRedirect('success', 'Reply sent.', url('admin/support-tickets/' . $id));
    }

    public function updateStatus(int $id): void
    {
        $this->validateCsrf();

        $status = Request::string('status');

        if (!in_array($status, ['open', 'pending', 'closed'], true)) {
            Response::abort(422, 'Invalid status');
        }

        SupportTicket::update($id, ['status' => $status]);
        $this->flashAndRedirect('success', 'Status updated.', url('admin/support-tickets/' . $id));
    }
}
