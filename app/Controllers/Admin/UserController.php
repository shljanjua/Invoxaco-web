<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\User;

class UserController extends Controller
{
    public function index(): void
    {
        $page = max(1, (int) Request::input('page', 1));
        $search = Request::string('search');

        $this->view('admin/users/index', [
            'pageTitle' => 'Users',
            'users' => User::paginateAll($page, 20, $search),
            'search' => $search,
            'page' => $page,
        ], 'layouts/admin');
    }

    public function edit(int $id): void
    {
        $user = User::find($id);

        if (!$user) {
            Response::abort(404, 'User not found');
        }

        $this->view('admin/users/edit', [
            'pageTitle' => 'Edit User',
            'user' => $user,
        ], 'layouts/admin');
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $user = User::find($id);

        if (!$user) {
            Response::abort(404, 'User not found');
        }

        $role = Request::string('role') === 'admin' ? 'admin' : 'user';
        $plan = in_array(Request::string('plan'), ['free', 'pro', 'premium'], true) ? Request::string('plan') : 'free';
        $isBanned = Request::input('is_banned') ? 1 : 0;

        User::update($id, [
            'role' => $role,
            'plan' => $plan,
            'is_banned' => $isBanned,
        ]);

        $this->flashAndRedirect('success', 'User updated.', url('admin/users'));
    }

    public function destroy(int $id): void
    {
        $this->validateCsrf();

        if ($id === Auth::id()) {
            $this->flashAndRedirect('error', 'You cannot delete your own account.', url('admin/users'));
        }

        User::delete($id);
        $this->flashAndRedirect('success', 'User deleted.', url('admin/users'));
    }
}
