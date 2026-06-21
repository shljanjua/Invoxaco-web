<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index(): void
    {
        $this->view('admin/categories/index', [
            'pageTitle' => 'Categories',
            'categories' => Category::withTemplateCounts(),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();

        $name = Request::string('name');

        if ($name === '') {
            $this->flashAndRedirect('error', 'Name is required.', url('admin/categories'));
        }

        Category::create([
            'name' => $name,
            'slug' => slugify($name),
            'description' => Request::string('description') ?: null,
            'icon' => Request::string('icon') ?: null,
            'sort_order' => (int) Request::input('sort_order', 0),
        ]);

        $this->flashAndRedirect('success', 'Category created.', url('admin/categories'));
    }

    public function update(int $id): void
    {
        $this->validateCsrf();

        $name = Request::string('name');

        Category::update($id, [
            'name' => $name,
            'slug' => slugify($name),
            'description' => Request::string('description') ?: null,
            'icon' => Request::string('icon') ?: null,
            'sort_order' => (int) Request::input('sort_order', 0),
        ]);

        $this->flashAndRedirect('success', 'Category updated.', url('admin/categories'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        Category::delete($id);
        $this->flashAndRedirect('success', 'Category deleted.', url('admin/categories'));
    }
}
