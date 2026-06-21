<?php

namespace App\Services;

use App\Models\User;

class PlanLimiter
{
    public static function plan(array $user): array
    {
        $plans = require __DIR__ . '/../Config/plans.php';

        return $plans[$user['plan']] ?? $plans['free'];
    }

    public static function canCreateDocument(array $user): bool
    {
        $plan = self::plan($user);

        if ($plan['document_limit'] === null) {
            return true;
        }

        return User::documentsUsedThisMonth((int) $user['id']) < $plan['document_limit'];
    }

    public static function remainingDocuments(array $user): ?int
    {
        $plan = self::plan($user);

        if ($plan['document_limit'] === null) {
            return null;
        }

        return max(0, $plan['document_limit'] - User::documentsUsedThisMonth((int) $user['id']));
    }

    public static function shouldWatermark(array $user): bool
    {
        return self::plan($user)['watermark'];
    }

    public static function canUseFeature(array $user, string $feature): bool
    {
        return (bool) (self::plan($user)[$feature] ?? false);
    }

    public static function canAccessTemplate(array $user, string $templatePlan): bool
    {
        $order = ['free' => 0, 'pro' => 1, 'premium' => 2];

        return ($order[$user['plan']] ?? 0) >= ($order[$templatePlan] ?? 0);
    }
}
