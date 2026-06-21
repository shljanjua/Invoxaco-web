<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Models\DocumentTemplate;
use App\Models\WaitlistEntry;
use App\Services\SeoService;

class GeneratorController extends Controller
{
    public function catalog(): void
    {
        $search = Request::string('search');
        $categories = Category::withTemplateCounts();
        $templates = $search !== '' ? DocumentTemplate::search($search) : DocumentTemplate::all('sort_order', 'ASC');

        $this->view('generators/catalog', [
            'metaTitle' => 'All Document Generators - 110+ Free Templates | Invoxaco',
            'metaDescription' => 'Browse 110+ free document generators for invoices, contracts, HR, real estate, construction, marketing, and startup paperwork.',
            'categories' => $categories,
            'templates' => $templates,
            'search' => $search,
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Generators', 'url' => url('generators')],
            ])],
        ]);
    }

    public function category(string $slug): void
    {
        $category = Category::findBy('slug', $slug);

        if (!$category) {
            Response::abort(404, 'Category not found');
        }

        $templates = DocumentTemplate::byCategory((int) $category['id']);

        $this->view('generators/category', [
            'metaTitle' => $category['name'] . ' Generators - Invoxaco',
            'metaDescription' => $category['description'] ?? ('Free ' . $category['name'] . ' generators from Invoxaco.'),
            'category' => $category,
            'templates' => $templates,
            'jsonLd' => [SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Generators', 'url' => url('generators')],
                ['name' => $category['name'], 'url' => url('generators/category/' . $category['slug'])],
            ])],
        ]);
    }

    public function show(string $slug): void
    {
        $template = DocumentTemplate::findBySlug($slug);

        if (!$template) {
            Response::abort(404, 'Generator not found');
        }

        if ($template['is_built']) {
            if (Auth::check()) {
                $this->redirect(url('documents/create/' . $slug));
            }
            $this->redirect(url('register') . '?next=' . urlencode('/documents/create/' . $slug));
        }

        $category = Category::find((int) $template['category_id']);
        $faqs = DocumentTemplate::decodeFaqs($template);

        $jsonLd = [
            SeoService::breadcrumbSchema([
                ['name' => 'Home', 'url' => url()],
                ['name' => 'Generators', 'url' => url('generators')],
                ['name' => $template['name'], 'url' => url('generators/' . $template['slug'])],
            ]),
        ];

        if (!empty($faqs)) {
            $jsonLd[] = SeoService::faqSchema($faqs);
        }

        $this->view('generators/coming-soon', [
            'metaTitle' => $template['meta_title'] ?? ($template['name'] . ' - Invoxaco'),
            'metaDescription' => $template['meta_description'] ?? $template['short_description'],
            'template' => $template,
            'category' => $category,
            'faqs' => $faqs,
            'jsonLd' => $jsonLd,
        ]);
    }

    public function notify(string $slug): void
    {
        $this->validateCsrf();
        $template = DocumentTemplate::findBySlug($slug);

        if (!$template) {
            Response::abort(404, 'Generator not found');
        }

        $email = strtolower(Request::string('email'));

        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                WaitlistEntry::create(['template_id' => $template['id'], 'email' => $email]);
            } catch (\PDOException) {
                // already on the waitlist for this generator - ignore duplicate
            }
        }

        $this->flashAndRedirect('success', "Thanks! We'll email you when the {$template['name']} is live.", url('generators/' . $slug));
    }
}
