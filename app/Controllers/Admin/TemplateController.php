<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Category;
use App\Models\DocumentTemplate;

class TemplateController extends Controller
{
    public function index(): void
    {
        $this->view('admin/generators/index', [
            'pageTitle' => 'Generators',
            'templates' => DocumentTemplate::allWithCategory(),
        ], 'layouts/admin');
    }

    public function create(): void
    {
        $this->view('admin/generators/form', [
            'pageTitle' => 'New Generator',
            'template' => null,
            'categories' => Category::all('name', 'ASC'),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        DocumentTemplate::create($this->collect());
        $this->flashAndRedirect('success', 'Generator created.', url('admin/generators'));
    }

    public function edit(int $id): void
    {
        $template = DocumentTemplate::find($id);

        if (!$template) {
            Response::abort(404, 'Generator not found');
        }

        $this->view('admin/generators/form', [
            'pageTitle' => 'Edit Generator',
            'template' => $template,
            'categories' => Category::all('name', 'ASC'),
        ], 'layouts/admin');
    }

    public function update(int $id): void
    {
        $this->validateCsrf();

        if (!DocumentTemplate::find($id)) {
            Response::abort(404, 'Generator not found');
        }

        DocumentTemplate::update($id, $this->collect());
        $this->flashAndRedirect('success', 'Generator updated.', url('admin/generators'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        DocumentTemplate::delete($id);
        $this->flashAndRedirect('success', 'Generator deleted.', url('admin/generators'));
    }

    private function collect(): array
    {
        $name = Request::string('name');
        $fieldsSchema = Request::string('fields_schema');
        $faqs = Request::string('faqs');

        return [
            'category_id' => (int) Request::input('category_id'),
            'name' => $name,
            'slug' => Request::string('slug') ?: slugify($name),
            'short_description' => Request::string('short_description') ?: null,
            'description' => Request::string('description') ?: null,
            'icon' => Request::string('icon') ?: null,
            'plan_required' => in_array(Request::string('plan_required'), ['free', 'pro', 'premium'], true) ? Request::string('plan_required') : 'free',
            'fields_schema' => $fieldsSchema !== '' ? $fieldsSchema : null,
            'faqs' => $faqs !== '' ? $faqs : null,
            'is_built' => Request::input('is_built') ? 1 : 0,
            'is_active' => Request::input('is_active') ? 1 : 0,
            'meta_title' => Request::string('meta_title') ?: null,
            'meta_description' => Request::string('meta_description') ?: null,
            'sort_order' => (int) Request::input('sort_order', 0),
        ];
    }
}
