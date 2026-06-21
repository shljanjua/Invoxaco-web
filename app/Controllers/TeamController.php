<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Request;
use App\Models\Team;
use App\Models\TeamMember;
use App\Models\User;
use App\Services\MailService;
use App\Services\PlanLimiter;

class TeamController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $plan = PlanLimiter::plan($user);

        if ($plan['team_members'] === 0) {
            $this->flashAndRedirect('error', 'Team collaboration is available on the Premium plan.', url('pricing'));
        }

        $team = Team::forOwner((int) $user['id']);

        if (!$team) {
            $teamId = Team::create(['owner_id' => $user['id'], 'name' => ($user['company_name'] ?: $user['name']) . "'s Team"]);
            $team = Team::find($teamId);
        }

        $this->view('team/index', [
            'metaTitle' => 'Team - Invoxaco',
            'robotsMeta' => 'noindex,nofollow',
            'team' => $team,
            'members' => TeamMember::forTeam((int) $team['id']),
            'plan' => $plan,
        ]);
    }

    public function invite(): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $plan = PlanLimiter::plan($user);

        if ($plan['team_members'] === 0) {
            $this->flashAndRedirect('error', 'Team collaboration is available on the Premium plan.', url('pricing'));
        }

        $team = Team::forOwner((int) $user['id']);

        if ($plan['team_members'] !== null && count(TeamMember::forTeam((int) $team['id'])) >= $plan['team_members']) {
            $this->flashAndRedirect('error', 'You have reached your team member limit.', url('team'));
        }

        $email = strtolower(Request::string('email'));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flashAndRedirect('error', 'Please enter a valid email address.', url('team'));
        }

        $token = bin2hex(random_bytes(20));
        $existingUser = User::findBy('email', $email);

        TeamMember::create([
            'team_id' => $team['id'],
            'user_id' => $existingUser['id'] ?? null,
            'email' => $email,
            'role' => 'member',
            'status' => 'invited',
            'invite_token' => $token,
        ]);

        $body = \App\Core\View::render('emails/team-invite', [
            'inviterName' => $user['name'],
            'acceptUrl' => url('team/accept/' . $token),
        ], 'layouts/email');

        (new MailService())->send($email, $email, $user['name'] . ' invited you to Invoxaco', $body);

        $this->flashAndRedirect('success', 'Invitation sent to ' . $email . '.', url('team'));
    }

    public function remove(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $team = Team::forOwner((int) $user['id']);

        if ($team) {
            $member = TeamMember::find($id);
            if ($member && (int) $member['team_id'] === (int) $team['id']) {
                TeamMember::delete($id);
            }
        }

        $this->flashAndRedirect('success', 'Team member removed.', url('team'));
    }

    public function accept(string $token): void
    {
        $member = TeamMember::findByToken($token);

        if (!$member) {
            $this->flashAndRedirect('error', 'This invitation link is invalid or has expired.', url('team'));
        }

        $user = Auth::user();
        TeamMember::update((int) $member['id'], [
            'user_id' => $user['id'],
            'status' => 'active',
            'invite_token' => null,
        ]);

        $this->flashAndRedirect('success', 'You have joined the team.', url('dashboard'));
    }
}
