<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\DocumentTemplate;
use App\Services\SeoService;

class HomeController extends Controller
{
    public function index(): void
    {
        $this->view('home/index', [
            'metaTitle' => 'Invoxaco - Free Online Invoice & Business Document Generator',
            'metaDescription' => 'Create invoices, quotes, contracts & 110+ business documents online. Save, download as PDF or Word in minutes. Free to start, no credit card.',
            'metaKeywords' => 'invoice generator, free invoice maker, business document generator, invoice template, quotation generator, contract generator, CV builder, salary slip generator, online invoicing software',
            'categories' => Category::withTemplateCounts(),
            'builtTemplates' => DocumentTemplate::builtTemplates(),
            'posts' => BlogPost::published(3),
            'jsonLd' => [SeoService::softwareApplicationSchema()],
        ]);
    }
}
