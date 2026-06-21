<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\SeoSetting;

class SeoController extends Controller
{
    private const PAGE_KEYS = [
        'home', 'features', 'pricing', 'templates', 'generators', 'about',
        'contact', 'faq', 'help', 'blog',
    ];

    public function index(): void
    {
        $pages = [];

        foreach (self::PAGE_KEYS as $key) {
            $pages[$key] = SeoSetting::forPage($key);
        }

        $this->view('admin/seo/index', [
            'pageTitle' => 'SEO Settings',
            'pages' => $pages,
        ], 'layouts/admin');
    }

    public function update(string $pageKey): void
    {
        $this->validateCsrf();

        if (!in_array($pageKey, self::PAGE_KEYS, true)) {
            $this->flashAndRedirect('error', 'Unknown page.', url('admin/seo'));
        }

        SeoSetting::upsert($pageKey, [
            'meta_title' => Request::string('meta_title') ?: null,
            'meta_description' => Request::string('meta_description') ?: null,
            'og_image' => Request::string('og_image') ?: null,
            'canonical_url' => Request::string('canonical_url') ?: null,
            'robots' => Request::string('robots') ?: 'index,follow',
        ]);

        $this->flashAndRedirect('success', 'SEO settings updated.', url('admin/seo'));
    }
}
