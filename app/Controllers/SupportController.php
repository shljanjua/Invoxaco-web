<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\SupportTicket;
use App\Models\SupportTicketReply;
use App\Services\PlanLimiter;

class SupportController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();

        $this->view('support/index', [
            'metaTitle' => 'Support Tickets - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'tickets' => SupportTicket::forUser((int) $user['id']),
        ]);
    }

    public function create(): void
    {
        $this->view('support/create', [
            'metaTitle' => 'New Support Ticket - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = Auth::user();

        $data = [
            'subject' => Request::string('subject'),
            'message' => Request::string('message'),
        ];

        $validator = Validator::make($data)
            ->required('subject', 'Subject')
            ->required('message', 'Message');

        if ($validator->fails()) {
            $this->flashAndRedirect('error', $validator->first(), url('support/create'));
        }

        $priority = PlanLimiter::canUseFeature($user, 'priority_support') ? 'high' : 'medium';

        $id = SupportTicket::create([
            'user_id' => $user['id'],
            'subject' => $data['subject'],
            'message' => $data['message'],
            'status' => 'open',
            'priority' => $priority,
        ]);

        $this->flashAndRedirect('success', 'Support ticket submitted. We will respond soon.', url('support/' . $id));
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $ticket = SupportTicket::findForUser($id, (int) $user['id']);

        if (!$ticket) {
            Response::abort(404, 'Ticket not found');
        }

        $this->view('support/show', [
            'metaTitle' => $ticket['subject'] . ' - Support - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'ticket' => $ticket,
            'replies' => SupportTicketReply::forTicket($id),
        ]);
    }

    public function reply(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $ticket = SupportTicket::findForUser($id, (int) $user['id']);

        if (!$ticket) {
            Response::abort(404, 'Ticket not found');
        }

        $message = Request::string('message');

        if ($message !== '') {
            SupportTicketReply::create([
                'ticket_id' => $id,
                'user_id' => $user['id'],
                'is_admin' => 0,
                'message' => $message,
            ]);

            SupportTicket::update($id, ['status' => 'pending']);
        }

        $this->flashAndRedirect('success', 'Reply added.', url('support/' . $id));
    }
}
