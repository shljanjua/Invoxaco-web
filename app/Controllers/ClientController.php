<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Validator;
use App\Models\Client;
use App\Models\Document;

class ClientController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $search = Request::string('search');

        $this->view('clients/index', [
            'metaTitle' => 'My Clients - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'clients' => Client::forUser((int) $user['id'], $search),
            'search' => $search,
        ]);
    }

    public function create(): void
    {
        $this->view('clients/form', [
            'metaTitle' => 'Add Client - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'client' => null,
            'formAction' => url('clients'),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = Auth::user();

        $data = $this->collect();

        $validator = Validator::make($data)->required('name', 'Client name');

        if ($validator->fails()) {
            $this->flashAndRedirect('error', $validator->first(), url('clients/create'));
        }

        $data['user_id'] = $user['id'];
        $id = Client::create($data);

        $this->flashAndRedirect('success', 'Client added.', url('clients/' . $id));
    }

    public function edit(int $id): void
    {
        $user = Auth::user();
        $client = Client::findForUser($id, (int) $user['id']);

        if (!$client) {
            Response::abort(404, 'Client not found');
        }

        $this->view('clients/form', [
            'metaTitle' => 'Edit ' . $client['name'] . ' - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'client' => $client,
            'formAction' => url('clients/' . $id),
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $client = Client::findForUser($id, (int) $user['id']);

        if (!$client) {
            Response::abort(404, 'Client not found');
        }

        $data = $this->collect();

        $validator = Validator::make($data)->required('name', 'Client name');

        if ($validator->fails()) {
            $this->flashAndRedirect('error', $validator->first(), url('clients/' . $id . '/edit'));
        }

        Client::update($id, $data);

        $this->flashAndRedirect('success', 'Client updated.', url('clients/' . $id));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $client = Client::findForUser($id, (int) $user['id']);

        if (!$client) {
            Response::abort(404, 'Client not found');
        }

        Client::delete($id);
        $this->flashAndRedirect('success', 'Client deleted.', url('clients'));
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $client = Client::findForUser($id, (int) $user['id']);

        if (!$client) {
            Response::abort(404, 'Client not found');
        }

        $this->view('clients/show', [
            'metaTitle' => $client['name'] . ' - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'client' => $client,
            'documents' => Document::forClient($id, (int) $user['id']),
        ]);
    }

    private function collect(): array
    {
        return [
            'name' => Request::string('name'),
            'email' => Request::string('email') ?: null,
            'phone' => Request::string('phone') ?: null,
            'company' => Request::string('company') ?: null,
            'address' => Request::string('address') ?: null,
            'notes' => Request::string('notes') ?: null,
        ];
    }
}
