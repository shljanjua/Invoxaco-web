<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\User;
use App\Services\FileUploader;

class CompanyProfileController extends Controller
{
    public function index(): void
    {
        $this->view('settings/company', [
            'metaTitle' => 'Company Settings - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'user' => Auth::user(),
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();
        $user = Auth::user();

        $data = [
            'company_name' => Request::string('company_name') ?: null,
            'phone' => Request::string('phone') ?: null,
            'address' => Request::string('address') ?: null,
            'tax_number' => Request::string('tax_number') ?: null,
            'currency' => Request::string('currency') ?: 'USD',
            'bank_name' => Request::string('bank_name') ?: null,
            'bank_account_no' => Request::string('bank_account_no') ?: null,
            'website' => Request::string('website') ?: null,
        ];

        if ($logo = Request::file('company_logo')) {
            try {
                $filename = FileUploader::storeImage($logo, 'logos');
                if ($filename) {
                    FileUploader::delete('logos', $user['company_logo']);
                    $data['company_logo'] = $filename;
                }
            } catch (\RuntimeException $e) {
                $this->flashAndRedirect('error', $e->getMessage(), url('settings'));
            }
        }

        if ($signature = Request::file('signature')) {
            try {
                $filename = FileUploader::storeImage($signature, 'signatures');
                if ($filename) {
                    FileUploader::delete('signatures', $user['signature_path']);
                    $data['signature_path'] = $filename;
                }
            } catch (\RuntimeException $e) {
                $this->flashAndRedirect('error', $e->getMessage(), url('settings'));
            }
        }

        User::update((int) $user['id'], $data);

        $this->flashAndRedirect('success', 'Company settings updated.', url('settings'));
    }
}
