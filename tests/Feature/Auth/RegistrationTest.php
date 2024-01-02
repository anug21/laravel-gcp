<?php

test('New users can register', function () {
    $response = $this->post(route('register'), [
        'email' => 'test@founderandlightning.com',
        'password' => 'Admin@12-3',
        'password_confirmation' => 'Admin@12-3',
    ]);

    $response->assertCreated();
    $response->assertSee(__('messages.user.registered'));
    $this->assertDatabaseHas('users', [
        'email' => 'test@founderandlightning.com'
    ]);
});

test('Users with invitation can register', function () {
    $invitation = \App\Services\UserInvitationService::create([
        'email' => 'test@founderandlightning.com',
        'role' => 'user',
    ]);

    $response = $this->post(route('register'), [
        'invitation_key' => $invitation->signature,
        'password' => 'Admin@12-3',
        'password_confirmation' => 'Admin@12-3',
    ]);

    $response->assertCreated();
    $response->assertSee(__('messages.user.registered'));
    $this->assertDatabaseHas('users', [
        'email' => 'test@founderandlightning.com'
    ]);
});

test('Password format validations', function () {
    $responseLowerCasePassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $responseLowerCasePassword->assertInvalid();

    $responseUpperCasePassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSWORD',
        'password_confirmation' => 'PASSWORD',
    ]);

    $responseUpperCasePassword->assertInvalid();

    $responseMixedCasePassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSword',
        'password_confirmation' => 'PASSword',
    ]);

    $responseMixedCasePassword->assertInvalid();

    $responseMixedCaseAndNumberPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSword1',
        'password_confirmation' => 'PASSword1',
    ]);

    $responseMixedCaseAndNumberPassword->assertInvalid();

    $responseSmallPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'Sword1@',
        'password_confirmation' => 'Sword1@',
    ]);

    $responseSmallPassword->assertInvalid();

    $responseConsecutiveCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@sssword1',
        'password_confirmation' => 'P@sssword1',
    ]);

    $responseConsecutiveCharsPassword->assertInvalid();

    $responseSequentialIncCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword123',
        'password_confirmation' => 'P@sssword123',
    ]);

    $responseSequentialIncCharsPassword->assertInvalid();

    $responseSequentialDecCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword321',
        'password_confirmation' => 'P@sssword321',
    ]);

    $responseSequentialDecCharsPassword->assertInvalid();

    $responseSequentialIncKeyboardCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword1qwer',
        'password_confirmation' => 'P@sssword1qwer',
    ]);

    $responseSequentialIncKeyboardCharsPassword->assertInvalid();

    $responseSequentialDecKeyboardCharsPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword1/.,m',
        'password_confirmation' => 'P@sssword1/.,m',
    ]);

    $responseSequentialDecKeyboardCharsPassword->assertInvalid();

    $responsePasswordContainingEmail = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PAss-test-word@12-3',
        'password_confirmation' => 'PAss-test-word@12-3',
    ]);

    $responsePasswordContainingEmail->assertInvalid();

    $responseValidPassword = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSword@12-3',
        'password_confirmation' => 'PASSword@12-3',
    ]);

    $responseValidPassword->assertCreated();
    $responseValidPassword->assertSee(__('messages.user.registered'));
});
