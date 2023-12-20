<?php

namespace App\Mail;

class UserInvitationMail extends BaseEmail
{
    public $view = 'emails.user-invitation';

    protected function createSubject()
    {
        $this->subject = __('messages.user.invitation_email_subject', ['service' => config('app.name')]);
    }
}
