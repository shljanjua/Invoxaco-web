<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\ContactMessage;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('admin/contact-messages/index', [
            'pageTitle' => 'Contact Messages',
            'messages' => ContactMessage::all('created_at', 'DESC'),
        ], 'layouts/admin');
    }

    public function show(int $id): void
    {
        $message = ContactMessage::find($id);

        if (!$message) {
            Response::abort(404, 'Message not found');
        }

        if ($message['status'] === 'new') {
            ContactMessage::update($id, ['status' => 'read']);
            $message['status'] = 'read';
        }

        $this->view('admin/contact-messages/show', [
            'pageTitle' => 'Contact Message',
            'message' => $message,
        ], 'layouts/admin');
    }

    public function updateStatus(int $id): void
    {
        $this->validateCsrf();

        $status = Request::string('status');

        if (!in_array($status, ['new', 'read', 'replied'], true)) {
            Response::abort(422, 'Invalid status');
        }

        ContactMessage::update($id, ['status' => $status]);
        $this->flashAndRedirect('success', 'Status updated.', url('admin/contact-messages/' . $id));
    }
}
