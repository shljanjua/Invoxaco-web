<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Validator;
use App\Models\ContactMessage;
use App\Services\SeoService;

class ContactController extends Controller
{
    public function show(): void
    {
        $this->view('pages/contact', [
            'metaTitle' => 'Contact Us - Invoxaco',
            'metaDescription' => 'Get in touch with the Invoxaco team for support, sales, or partnership inquiries.',
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Contact', 'url' => url('contact')],
            ])],
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();

        $data = [
            'name' => Request::string('name'),
            'email' => Request::string('email'),
            'subject' => Request::string('subject'),
            'message' => Request::string('message'),
        ];

        $validator = Validator::make($data)
            ->required('name', 'Name')
            ->required('email', 'Email')
            ->email('email')
            ->required('message', 'Message');

        if ($validator->fails()) {
            $this->flashAndRedirect('error', $validator->first(), url('contact'));
        }

        ContactMessage::create($data + ['status' => 'new']);

        $this->flashAndRedirect('success', "Thanks for reaching out! We'll get back to you soon.", url('contact'));
    }
}
