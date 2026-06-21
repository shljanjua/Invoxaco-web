<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\SmtpSetting;
use App\Services\MailService;

class SmtpController extends Controller
{
    public function index(): void
    {
        $this->view('admin/smtp/index', [
            'pageTitle' => 'SMTP Settings',
            'smtp' => SmtpSetting::active(),
        ], 'layouts/admin');
    }

    public function update(): void
    {
        $this->validateCsrf();

        $data = [
            'host' => Request::string('host'),
            'port' => (int) Request::input('port', 587),
            'encryption' => Request::string('encryption') ?: 'tls',
            'username' => Request::string('username') ?: null,
            'from_address' => Request::string('from_address'),
            'from_name' => Request::string('from_name'),
            'is_active' => 1,
        ];

        $password = Request::string('password');
        if ($password !== '') {
            $data['password'] = $password;
        }

        $existing = SmtpSetting::active();

        if ($existing) {
            SmtpSetting::update($existing['id'], $data);
        } else {
            $data['password'] = $data['password'] ?? '';
            SmtpSetting::create($data);
        }

        $this->flashAndRedirect('success', 'SMTP settings saved.', url('admin/smtp'));
    }

    public function test(): void
    {
        $this->validateCsrf();

        $user = Auth::user();
        $sent = (new MailService())->send(
            $user['email'],
            $user['name'],
            'Invoxaco SMTP Test',
            '<p>This is a test email confirming your SMTP settings are working correctly.</p>'
        );

        $this->flashAndRedirect(
            $sent ? 'success' : 'error',
            $sent ? 'Test email sent successfully.' : 'Failed to send test email. Check your SMTP settings.',
            url('admin/smtp')
        );
    }
}
