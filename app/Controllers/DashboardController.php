<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Client;
use App\Models\Document;
use App\Services\PlanLimiter;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $documents = Document::forUser((int) $user['id']);
        $recent = array_slice($documents, 0, 8);

        $thisMonth = Document::countInMonth((int) $user['id'], 0);
        $lastMonth = Document::countInMonth((int) $user['id'], 1);
        $monthChange = $lastMonth > 0
            ? round((($thisMonth - $lastMonth) / $lastMonth) * 100)
            : ($thisMonth > 0 ? 100 : 0);

        $this->view('dashboard/index', [
            'metaTitle' => 'Dashboard - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'user' => $user,
            'documents' => $recent,
            'totalDocuments' => count($documents),
            'plan' => PlanLimiter::plan($user),
            'remaining' => PlanLimiter::remainingDocuments($user),
            'clientCount' => Client::count(['user_id' => $user['id']]),
            'monthlySeries' => Document::monthlySeries((int) $user['id'], 6),
            'statusBreakdown' => Document::statusBreakdown((int) $user['id']),
            'thisMonthCount' => $thisMonth,
            'lastMonthCount' => $lastMonth,
            'monthChange' => $monthChange,
        ]);
    }
}
