<?php

namespace App\Middleware;

use App\Core\Auth;
use App\Core\Response;

class AuthMiddleware
{
    public function handle(): void
    {
        if (!Auth::check() && !Auth::attemptRememberLogin()) {
            Response::redirect(url('login'));
        }
    }
}
