<?php

use App\Services\UserInvitationService;

test('Can verify invitation with correct signature', function () {
    $this->be(createSuperAdmin());

    $invitation = (new UserInvitationService())->create([
        'email' => 'test@laravel.com',
        'role' => 'user',
    ]);

    Auth::logout();

    $this->get(route('users.invitation.verify', $invitation->signature))
        ->assertRedirectContains(config('frontend.invitation_success_redirect'))
        ->assertRedirectContains($invitation->signature);
});

test('Cannot verify invitation with incorrect signature', function () {
    $this->get(route('users.invitation.verify', 'non-existing-signature'))
        ->assertRedirectContains(config('frontend.invitation_fail_redirect'));
});

test('Cannot verify expired invitation', function () {
    $this->be(createSuperAdmin());

    $invitation = (new UserInvitationService())->create([
        'email' => 'test@laravel.com',
        'role' => 'user',
    ]);

    $invitation->expires_at = (new DateTime())->modify('-1second');
    $invitation->save();

    Auth::logout();

    $this->get(route('users.invitation.verify', $invitation->signature))
        ->assertRedirectContains(config('frontend.invitation_fail_redirect'));
});
