<?php

uses(Tests\TestCase::class)->in( __DIR__ );

test('User can resend invite immediately after signup - with route url', function () {
    $this->withHeader('Accept', 'application/json')
        ->post('api/v1/invitations/resend/'. fake()->email)->assertOk();
});

test('User can resend invite immediately after signup - with route name', function () {
    $this->withHeader('Accept', 'application/json')
        ->post(route('invite.resend', ['email' => fake()->email]))
        ->assertOk();
});