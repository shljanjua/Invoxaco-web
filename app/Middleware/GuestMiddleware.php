<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class GuestMiddleware
{
    public function handle(): void
    {
        if (Auth::check()) {
            Response::redirect(url(Auth::isAdmin() ? 'admin/dashboard' : 'dashboard'));
        }
    }
}
