<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Document;
use App\Services\PlanLimiter;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $documents = Document::forUser((int) $user['id']);
        $recent = array_slice($documents, 0, 8);

        $this->view('dashboard/index', [
            'metaTitle' => 'Dashboard - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'user' => $user,
            'documents' => $recent,
            'totalDocuments' => count($documents),
            'plan' => PlanLimiter::plan($user),
            'remaining' => PlanLimiter::remainingDocuments($user),
        ]);
    }
}
