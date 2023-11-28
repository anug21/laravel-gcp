<?php

test('New users can register', function () {
    $response = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'Admin@12-3',
        'password_confirmation' => 'Admin@12-3',
    ]);

    $response->assertCreated();
    $response->assertSee(__('messages.user.registered'));
    $this->assertDatabaseHas('users', [
        'first_name' => 'Test'
    ]);
});

test('Password format validations', function () {
    $response_lower_case_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response_lower_case_password->assertInvalid();

    $response_upper_case_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSWORD',
        'password_confirmation' => 'PASSWORD',
    ]);

    $response_upper_case_password->assertInvalid();

    $response_mixed_case_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSword',
        'password_confirmation' => 'PASSword',
    ]);

    $response_mixed_case_password->assertInvalid();

    $response_mixed_case_and_number_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSword1',
        'password_confirmation' => 'PASSword1',
    ]);

    $response_mixed_case_and_number_password->assertInvalid();

    $response_small_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'Sword1@',
        'password_confirmation' => 'Sword1@',
    ]);

    $response_small_password->assertInvalid();

    $response_consecutive_chars_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@sssword1',
        'password_confirmation' => 'P@sssword1',
    ]);

    $response_consecutive_chars_password->assertInvalid();

    $response_sequential_inc_chars_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword123',
        'password_confirmation' => 'P@sssword123',
    ]);

    $response_sequential_inc_chars_password->assertInvalid();

    $response_sequential_dec_chars_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword321',
        'password_confirmation' => 'P@sssword321',
    ]);

    $response_sequential_dec_chars_password->assertInvalid();

    $response_sequential_inc_keyboard_chars_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword1qwer',
        'password_confirmation' => 'P@sssword1qwer',
    ]);

    $response_sequential_inc_keyboard_chars_password->assertInvalid();

    $response_sequential_dec_keyboard_chars_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'P@ssword1/.,m',
        'password_confirmation' => 'P@sssword1/.,m',
    ]);

    $response_sequential_dec_keyboard_chars_password->assertInvalid();

    $response_valid_password = $this->post(route('register'), [
        'first_name' => 'Test',
        'email' => 'test@founderandlightning.com',
        'password' => 'PASSword@12-3',
        'password_confirmation' => 'PASSword@12-3',
    ]);

    $response_valid_password->assertCreated();
    $response_valid_password->assertSee(__('messages.user.registered'));
});
