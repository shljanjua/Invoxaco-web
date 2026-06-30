<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Core\Request;
use App\Models\StoreCategory;

class StoreCategoryController extends Controller
{
    public function index(): void
    {
        $this->view('admin/store-categories/index', [
            'pageTitle' => 'Store Categories',
            'categories' => StoreCategory::ordered(),
        ], 'layouts/admin');
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = trim(Request::string('name'));

        if ($name === '') {
            $this->flashAndRedirect('error', 'Category name is required.', url('admin/store-categories'));
        }

        StoreCategory::create([
            'name' => $name,
            'slug' => Request::string('slug') ?: slugify($name),
            'description' => Request::string('description') ?: null,
            'sort_order' => (int) Request::input('sort_order', 0),
        ]);

        $this->flashAndRedirect('success', 'Category created.', url('admin/store-categories'));
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $name = trim(Request::string('name'));

        StoreCategory::update($id, [
            'name' => $name,
            'slug' => Request::string('slug') ?: slugify($name),
            'description' => Request::string('description') ?: null,
            'sort_order' => (int) Request::input('sort_order', 0),
        ]);

        $this->flashAndRedirect('success', 'Category updated.', url('admin/store-categories'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();
        StoreCategory::delete($id);
        $this->flashAndRedirect('success', 'Category deleted.', url('admin/store-categories'));
    }
}
