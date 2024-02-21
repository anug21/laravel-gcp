<?php


beforeEach(function () {
    $this->user = createUser();
});

test('Get profile - with route url', function () {
    $this->withHeader('Accept', 'application/json')
    ->get('api/v1/users/1')->assertUnauthorized();
});

test('Get profile - with route name', function () {
    $this->withHeader('Accept', 'application/json')
        ->get(route('users.get', ['id' => 1]))
        ->assertUnauthorized();
});

test('Get profile user not found', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->get(route('users.get', ['id' => fake()->randomDigitNot($this->user->id)]))
        ->assertNotFound();
});

test('Get profile successfully', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->get(route('users.get', $this->user->id))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'first_name',
                'last_name',
                'email',
                'roles',
                'email_verified_at',
                'created_at',
                'updated_at',
            ],
        ]);
});

test('Update profile - with route url', function () {
    $this->withHeader('Accept', 'application/json')
        ->patch('api/v1/users/1')->assertUnauthorized();
});

test('Update profile - with route name', function () {
    $this->withHeader('Accept', 'application/json')
        ->patch(route('users.update', ['id' => 1]))
        ->assertUnauthorized();
});

test('Update profile user validation exception', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->patch(route('users.update', ['id' => fake()->randomDigitNot($this->user->id)]))
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'id'
            ],
        ]);
});

test('Update profile user validation exception - role not found', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->patch(route('users.update', [
            'id' => $this->user->id,
            'first_name' => fake()->firstName,
            'role' => 'not-found'
        ]))
        ->assertStatus(422)
        ->assertJsonStructure([
            'message',
            'errors' => [
                'role'
            ],
        ]);
});

test('Update profile user successfully', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->patch(route('users.update', [
            'id' => $this->user->id,
            'first_name' => fake()->firstName,
            'last_name' => fake()->lastName,
            'role' => Config::get('constants.roles')['super_admin']
        ]))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data' => [
                'id',
                'first_name',
                'last_name',
                'role'
            ],
        ]);
});

test('Delete user route exists - route url', function () {
    $this->withHeader('Accept', 'application/json')
        ->delete('api/v1/users/1')->assertUnauthorized();
});

test('Delete user route exists - route name', function () {
    $this->withHeader('Accept', 'application/json')
        ->delete(route('users.get', ['id' => 1]))
        ->assertUnauthorized();
});

test('Delete user validation exception - id not found', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->delete(route('users.update', ['id' => fake()->randomDigitNot($this->user->id)]))
        ->assertNotFound()
        ->assertJsonStructure([
            'message',
            'data'
        ]);
});

test('Delete user successfully', function () {
    $this->actingAs($this->user)
        ->withHeader('Accept', 'application/json')
        ->delete(route('users.update', ['id' => $this->user->id]))
        ->assertOk()
        ->assertJsonStructure([
            'message',
            'data'
        ]);
});