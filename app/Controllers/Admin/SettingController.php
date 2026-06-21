<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Setting;

class SettingController extends Controller
{
    private const KEYS = [
        'site_name', 'support_email', 'contact_phone', 'company_address',
        'google_analytics_id', 'facebook_pixel_id', 'social_twitter',
        'social_facebook', 'social_linkedin', 'maintenance_mode',
    ];

    public function index(): void
    {
        $settings = [];

        foreach (self::KEYS as $key) {
            $settings[$key] = Setting::get($key, '');
        }

        $this->view('admin/settings/index', [
            'pageTitle' => 'Website Settings',
            'settings' => $settings,
        ], 'layouts/admin');
    }

    public function update(): void
    {
        $this->validateCsrf();

        foreach (self::KEYS as $key) {
            Setting::set($key, Request::string($key));
        }

        $this->flashAndRedirect('success', 'Settings updated.', url('admin/settings'));
    }
}
