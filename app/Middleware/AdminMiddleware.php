<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class AdminMiddleware
{
    public function handle(): void
    {
        if (!Auth::check() && !Auth::attemptRememberLogin()) {
            Response::redirect(url('login'));
        }

        if (!Auth::isAdmin()) {
            Response::abort(403, 'You do not have access to the admin panel.');
        }
    }
}
