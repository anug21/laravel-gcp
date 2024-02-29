<?php

use Modules\UserInvitation\Http\Controllers\v1\UserInvitationController;

Route::group(['prefix' => 'api/v1', 'middleware' => ['auth:sanctum']], function () {
    Route::controller(UserInvitationController::class)
        ->prefix('/invitations')
        ->group(function () {
            Route::group(['middleware' => 'verified'], function () {
                Route::get('/', 'index')->name('invitations.index')->can('view users');
                Route::post('/', 'store')->name('invitations.store')->can('create user');
                Route::delete('/{id}', 'destroy')->name('invitations.destroy')->can('create user');
            });
        })
        ->group(function () {
            Route::post('/resend', 'resend')->name('invitations.resend');
        });
});