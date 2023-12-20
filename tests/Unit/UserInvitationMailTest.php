<?php

use App\Mail\UserInvitationMail;

test('User invitation mail', function () {
    $data = [
        'signature' => bin2hex(openssl_random_pseudo_bytes(32)),
    ];
    (new UserInvitationMail($data))->to(fake()->email)
        ->assertFrom(config('mail.from.address'), config('mail.from.name'))
        ->assertHasSubject(__('messages.user.invitation_email_subject', ['service' => config('app.name')]))
        ->assertSeeInHtml($data['signature']);
});
