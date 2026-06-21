<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Client;
use App\Models\Document;
use App\Models\DocumentTemplate;
use App\Services\DocxService;
use App\Services\GeneratorEngine;
use App\Services\MailService;
use App\Services\PdfService;
use App\Services\PlanLimiter;

class DocumentController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $search = Request::string('search');

        $this->view('documents/index', [
            'metaTitle' => 'My Documents - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'documents' => Document::forUser((int) $user['id'], $search),
            'search' => $search,
        ]);
    }

    public function create(string $slug): void
    {
        $user = Auth::user();
        $template = DocumentTemplate::findBySlug($slug);

        if (!$template || !$template['is_built']) {
            Response::abort(404, 'Generator not found');
        }

        if (!PlanLimiter::canAccessTemplate($user, $template['plan_required'])) {
            $this->flashAndRedirect('error', 'Upgrade your plan to access this premium template.', url('pricing'));
        }

        if (!PlanLimiter::canCreateDocument($user)) {
            $this->flashAndRedirect('error', 'You have reached your monthly document limit. Upgrade your plan for more.', url('pricing'));
        }

        $this->view('documents/form', [
            'metaTitle' => 'Create ' . $template['name'] . ' - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'template' => $template,
            'fields' => DocumentTemplate::decodeFields($template),
            'document' => null,
            'clients' => Client::forUser((int) $user['id']),
            'formAction' => url('documents'),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $template = DocumentTemplate::findBySlug(Request::string('template_slug'));

        if (!$template) {
            Response::abort(404, 'Generator not found');
        }

        if (!PlanLimiter::canCreateDocument($user)) {
            $this->flashAndRedirect('error', 'You have reached your monthly document limit. Upgrade your plan for more.', url('pricing'));
        }

        $fields = DocumentTemplate::decodeFields($template);
        $data = GeneratorEngine::collectFromRequest($fields);

        $documentId = Document::create([
            'user_id' => $user['id'],
            'template_id' => $template['id'],
            'client_id' => Request::input('client_id') ?: null,
            'title' => Request::string('title') ?: doc_title($template['name']),
            'document_number' => $data['invoice_number'] ?? $data['quote_number'] ?? $data['po_number'] ?? $data['receipt_number'] ?? null,
            'data' => json_encode($data),
            'status' => Request::string('status') === 'final' ? 'final' : 'draft',
            'watermarked' => PlanLimiter::shouldWatermark($user) ? 1 : 0,
            'accent_color' => $this->collectAccentColor(),
            'template_style' => $this->collectTemplateStyle(),
            'show_logo' => Request::input('show_logo') !== null ? 1 : 0,
        ]);

        $this->flashAndRedirect('success', 'Document saved.', url('documents/' . $documentId . '/edit'));
    }

    public function edit(int $id): void
    {
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        $template = DocumentTemplate::find((int) $document['template_id']);

        $this->view('documents/form', [
            'metaTitle' => 'Edit ' . $document['title'] . ' - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'template' => $template,
            'fields' => DocumentTemplate::decodeFields($template),
            'document' => $document,
            'documentData' => json_decode($document['data'], true) ?: [],
            'clients' => Client::forUser((int) $user['id']),
            'formAction' => url('documents/' . $id),
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        $template = DocumentTemplate::find((int) $document['template_id']);
        $fields = DocumentTemplate::decodeFields($template);
        $data = GeneratorEngine::collectFromRequest($fields);

        Document::update($id, [
            'client_id' => Request::input('client_id') ?: null,
            'title' => Request::string('title') ?: $document['title'],
            'data' => json_encode($data),
            'status' => Request::string('status') === 'final' ? 'final' : 'draft',
            'accent_color' => $this->collectAccentColor(),
            'template_style' => $this->collectTemplateStyle(),
            'show_logo' => Request::input('show_logo') !== null ? 1 : 0,
        ]);

        $this->flashAndRedirect('success', 'Document updated.', url('documents/' . $id . '/edit'));
    }

    public function autosave(int $id): void
    {
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            $this->json(['ok' => false], 404);
        }

        $template = DocumentTemplate::find((int) $document['template_id']);
        $fields = DocumentTemplate::decodeFields($template);
        $data = GeneratorEngine::collectFromRequest($fields);

        Document::update($id, [
            'data' => json_encode($data),
            'title' => Request::string('title') ?: $document['title'],
            'accent_color' => $this->collectAccentColor(),
            'template_style' => $this->collectTemplateStyle(),
            'show_logo' => Request::input('show_logo') !== null ? 1 : 0,
        ]);

        $this->json(['ok' => true, 'saved_at' => date('H:i:s')]);
    }

    public function duplicate(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        if (!PlanLimiter::canCreateDocument($user)) {
            $this->flashAndRedirect('error', 'You have reached your monthly document limit. Upgrade your plan for more.', url('pricing'));
        }

        $newId = Document::create([
            'user_id' => $user['id'],
            'template_id' => $document['template_id'],
            'client_id' => $document['client_id'],
            'title' => $document['title'] . ' (Copy)',
            'document_number' => $document['document_number'],
            'data' => $document['data'],
            'status' => 'draft',
            'watermarked' => PlanLimiter::shouldWatermark($user) ? 1 : 0,
            'accent_color' => $document['accent_color'],
            'template_style' => $document['template_style'],
            'show_logo' => $document['show_logo'],
        ]);

        $this->flashAndRedirect('success', 'Document duplicated.', url('documents/' . $newId . '/edit'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        Document::delete($id);
        $this->flashAndRedirect('success', 'Document deleted.', url('documents'));
    }

    public function pdf(int $id): void
    {
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        $html = $this->renderDocumentHtml($document, $user);
        PdfService::fromHtml($html, $this->pdfFilename($document));
    }

    public function docx(int $id): void
    {
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        if (!PlanLimiter::canUseFeature($user, 'docx_export')) {
            $this->flashAndRedirect('error', 'DOCX export is available on Pro and Premium plans.', url('pricing'));
        }

        $template = DocumentTemplate::find((int) $document['template_id']);
        $fields = DocumentTemplate::decodeFields($template);
        $data = json_decode($document['data'], true) ?: [];
        $logoPath = $user['company_logo'] ? __DIR__ . '/../../public/uploads/logos/' . $user['company_logo'] : null;

        $contents = DocxService::fromDocument($document['title'], $fields, $data, $logoPath);

        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Disposition: attachment; filename="' . $this->slugFilename($document['title']) . '.docx"');
        echo $contents;
        exit;
    }

    public function print(int $id): void
    {
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        echo $this->renderDocumentHtml($document, $user, true);
    }

    public function email(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        if (!PlanLimiter::canUseFeature($user, 'email_sending')) {
            $this->flashAndRedirect('error', 'Emailing documents is available on Pro and Premium plans.', url('pricing'));
        }

        $toEmail = Request::string('to_email');
        $toName = Request::string('to_name') ?: $toEmail;
        $format = Request::string('format') === 'docx' ? 'docx' : 'pdf';

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            $this->flashAndRedirect('error', 'Please enter a valid recipient email address.', url('documents/' . $id . '/edit'));
        }

        $template = DocumentTemplate::find((int) $document['template_id']);
        $fields = DocumentTemplate::decodeFields($template);
        $data = json_decode($document['data'], true) ?: [];

        if ($format === 'pdf') {
            $html = $this->renderDocumentHtml($document, $user);
            $content = base64_encode(PdfService::fromHtml($html, '', false));
            $filename = $this->slugFilename($document['title']) . '.pdf';
            $mime = 'application/pdf';
        } else {
            $logoPath = $user['company_logo'] ? __DIR__ . '/../../public/uploads/logos/' . $user['company_logo'] : null;
            $content = base64_encode(DocxService::fromDocument($document['title'], $fields, $data, $logoPath));
            $filename = $this->slugFilename($document['title']) . '.docx';
            $mime = 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
        }

        $body = View::render('emails/document', [
            'senderName' => $user['name'],
            'companyName' => $user['company_name'] ?: $user['name'],
            'documentTitle' => $document['title'],
            'templateName' => $template['name'],
            'message' => Request::string('message'),
        ], 'layouts/email');

        $sent = (new MailService())->send($toEmail, $toName, $document['title'] . ' from ' . ($user['company_name'] ?: $user['name']), $body, [
            ['content' => base64_decode($content), 'name' => $filename, 'mime' => $mime],
        ]);

        if ($sent) {
            $this->flashAndRedirect('success', 'Document emailed to ' . $toEmail . '.', url('documents/' . $id . '/edit'));
        }

        $this->flashAndRedirect('error', 'Failed to send email. Check SMTP settings.', url('documents/' . $id . '/edit'));
    }

    public function shared(string $token): void
    {
        $document = Document::findByShareToken($token);

        if (!$document) {
            Response::abort(404, 'This shared document link is invalid or has expired.');
        }

        $owner = \App\Models\User::find((int) $document['user_id']);

        $this->view('documents/shared', [
            'metaTitle' => $document['title'] . ' - Shared via Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'html' => $this->renderDocumentHtml($document, $owner, true),
            'document' => $document,
            'token' => $token,
        ]);
    }

    public function sharedPdf(string $token): void
    {
        $document = Document::findByShareToken($token);

        if (!$document) {
            Response::abort(404, 'This shared document link is invalid or has expired.');
        }

        $owner = \App\Models\User::find((int) $document['user_id']);
        $html = $this->renderDocumentHtml($document, $owner);
        PdfService::fromHtml($html, $this->pdfFilename($document));
    }

    public function enableShare(int $id): void
    {
        // helper used by the share toggle button in documents/form.php
        $this->validateCsrf();
        $user = Auth::user();
        $document = Document::findForUser($id, (int) $user['id']);

        if (!$document) {
            Response::abort(404, 'Document not found');
        }

        $token = $document['share_token'] ?: bin2hex(random_bytes(20));
        Document::update($id, ['share_token' => $token]);

        $this->json(['ok' => true, 'url' => url('share/' . $token)]);
    }

    private function renderDocumentHtml(array $document, array $user, bool $forBrowser = false): string
    {
        $template = DocumentTemplate::find((int) $document['template_id']);
        $fields = DocumentTemplate::decodeFields($template);
        $data = json_decode($document['data'], true) ?: [];

        $client = null;
        if (!empty($document['client_id'])) {
            $client = \App\Models\Client::find((int) $document['client_id']);
        }

        return View::renderRaw('documents/pdf-template', [
            'template' => $template,
            'fields' => $fields,
            'data' => $data,
            'document' => $document,
            'user' => $user,
            'client' => $client,
            'watermark' => PlanLimiter::shouldWatermark($user),
            'forBrowser' => $forBrowser,
        ], '');
    }

    private function collectAccentColor(): string
    {
        $color = Request::string('accent_color');

        return preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : '#2563eb';
    }

    private function collectTemplateStyle(): string
    {
        $style = Request::string('template_style');

        return in_array($style, ['modern', 'classic', 'minimal', 'bold'], true) ? $style : 'modern';
    }

    private function pdfFilename(array $document): string
    {
        return $this->slugFilename($document['title']) . '.pdf';
    }

    private function slugFilename(string $title): string
    {
        return slugify($title) ?: 'document';
    }
}
