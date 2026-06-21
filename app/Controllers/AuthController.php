<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\RateLimiter;
use App\Core\Request;
use App\Core\Session;
use App\Core\Validator;
use App\Models\ActivityLog;
use App\Models\PasswordReset;
use App\Models\User;
use App\Services\MailService;

class AuthController extends Controller
{
    public function showRegister(): void
    {
        $this->view('auth/register', ['metaTitle' => 'Sign Up - Invoxaco'], 'layouts/guest');
    }

    public function register(): void
    {
        $this->validateCsrf();

        $data = [
            'name' => Request::string('name'),
            'email' => strtolower(Request::string('email')),
            'password' => Request::string('password'),
            'password_confirmation' => Request::string('password_confirmation'),
        ];

        $validator = Validator::make($data)
            ->required('name', 'Name')
            ->required('email', 'Email')
            ->email('email')
            ->unique('email', User::class, 'Email')
            ->required('password', 'Password')
            ->min('password', 8, 'Password')
            ->matches('password_confirmation', 'password', 'Password confirmation');

        if ($validator->fails()) {
            Session::put('_old_input', $data);
            $this->flashAndRedirect('error', $validator->first(), url('register'));
        }

        $token = bin2hex(random_bytes(32));

        $userId = User::createUser([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'user',
            'plan' => 'free',
            'verification_token' => $token,
            'verification_expires_at' => date('Y-m-d H:i:s', time() + 86400),
        ]);

        ActivityLog::log($userId, 'register', 'New account created');

        $this->sendVerificationEmail($data['email'], $data['name'], $token);

        Auth::login(['id' => $userId, 'role' => 'user']);

        $this->flashAndRedirect('success', 'Welcome to Invoxaco! Please check your email to verify your account.', url('dashboard'));
    }

    public function showLogin(): void
    {
        $this->view('auth/login', ['metaTitle' => 'Log In - Invoxaco'], 'layouts/guest');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $email = strtolower(Request::string('email'));
        $password = Request::string('password');
        $remember = (bool) Request::input('remember');

        $rateKey = 'login:' . $email . ':' . Request::ip();

        if (RateLimiter::tooManyAttempts($rateKey, (int) ($_ENV['RATE_LIMIT_LOGIN_ATTEMPTS'] ?? 5), (int) ($_ENV['RATE_LIMIT_LOGIN_DECAY_MINUTES'] ?? 15))) {
            $this->flashAndRedirect('error', 'Too many login attempts. Please try again in a few minutes.', url('login'));
        }

        if (!Auth::attempt($email, $password)) {
            RateLimiter::hit($rateKey);
            $this->flashAndRedirect('error', 'Invalid email or password.', url('login'));
        }

        RateLimiter::clear($rateKey);

        $user = Auth::user();
        ActivityLog::log($user['id'], 'login', 'User logged in');

        if ($remember) {
            Auth::setRememberCookie((int) $user['id']);
        }

        $this->redirect(url($user['role'] === 'admin' ? 'admin/dashboard' : 'dashboard'));
    }

    public function logout(): void
    {
        $this->validateCsrf();

        $user = Auth::user();
        if ($user) {
            ActivityLog::log($user['id'], 'logout', 'User logged out');
        }

        Auth::logout();
        $this->redirect(url('/'));
    }

    public function showForgot(): void
    {
        $this->view('auth/forgot-password', ['metaTitle' => 'Forgot Password - Invoxaco'], 'layouts/guest');
    }

    public function forgot(): void
    {
        $this->validateCsrf();

        $email = strtolower(Request::string('email'));
        $user = User::findBy('email', $email);

        if ($user) {
            $token = PasswordReset::createToken($email);
            $resetUrl = url('reset-password/' . $token) . '?email=' . urlencode($email);

            $body = \App\Core\View::render('emails/password-reset', [
                'name' => $user['name'],
                'resetUrl' => $resetUrl,
            ], 'layouts/email');

            (new MailService())->send($email, $user['name'], 'Reset your Invoxaco password', $body);
        }

        $this->flashAndRedirect('success', 'If that email exists in our system, a reset link has been sent.', url('login'));
    }

    public function showReset(string $token): void
    {
        $this->view('auth/reset-password', [
            'token' => $token,
            'email' => Request::string('email'),
            'metaTitle' => 'Reset Password - Invoxaco',
        ], 'layouts/guest');
    }

    public function reset(): void
    {
        $this->validateCsrf();

        $email = strtolower(Request::string('email'));
        $token = Request::string('token');
        $password = Request::string('password');
        $confirmation = Request::string('password_confirmation');

        $resetRecord = PasswordReset::findValid($email, $token);

        if (!$resetRecord) {
            $this->flashAndRedirect('error', 'This password reset link is invalid or has expired.', url('forgot-password'));
        }

        if (mb_strlen($password) < 8 || $password !== $confirmation) {
            $this->flashAndRedirect('error', 'Passwords must match and be at least 8 characters.', url('reset-password/' . $token) . '?email=' . urlencode($email));
        }

        $user = User::findBy('email', $email);
        User::update($user['id'], ['password' => password_hash($password, PASSWORD_BCRYPT)]);
        PasswordReset::deleteForEmail($email);
        ActivityLog::log($user['id'], 'password_reset', 'Password reset completed');

        $this->flashAndRedirect('success', 'Your password has been reset. Please log in.', url('login'));
    }

    public function verifyEmail(string $token): void
    {
        $user = User::findBy('verification_token', $token);

        if (!$user || ($user['verification_expires_at'] && strtotime($user['verification_expires_at']) < time())) {
            $this->flashAndRedirect('error', 'This verification link is invalid or has expired.', url('dashboard'));
        }

        User::update($user['id'], [
            'email_verified_at' => date('Y-m-d H:i:s'),
            'verification_token' => null,
        ]);

        if (!Auth::check()) {
            Auth::login($user);
        }

        $this->flashAndRedirect('success', 'Your email has been verified. Welcome to Invoxaco!', url('dashboard'));
    }

    public function resendVerification(): void
    {
        $this->validateCsrf();
        $user = Auth::user();

        if ($user['email_verified_at']) {
            $this->flashAndRedirect('success', 'Your email is already verified.', url('dashboard'));
        }

        $token = bin2hex(random_bytes(32));
        User::update($user['id'], [
            'verification_token' => $token,
            'verification_expires_at' => date('Y-m-d H:i:s', time() + 86400),
        ]);

        $this->sendVerificationEmail($user['email'], $user['name'], $token);

        $this->flashAndRedirect('success', 'Verification email sent. Please check your inbox.', url('dashboard'));
    }

    private function sendVerificationEmail(string $email, string $name, string $token): void
    {
        $verifyUrl = url('verify-email/' . $token);

        $body = \App\Core\View::render('emails/welcome', [
            'name' => $name,
            'verifyUrl' => $verifyUrl,
        ], 'layouts/email');

        (new MailService())->send($email, $name, 'Welcome to Invoxaco - Verify your email', $body);
    }
}
