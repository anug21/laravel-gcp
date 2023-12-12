<?php

namespace App\Services;

use App\Models\UserInvitation;
use DateTime;
use Spatie\Permission\Models\Role;

class UserInvitationService
{
    public function create(array $data): UserInvitation
    {
        UserInvitation::where('email', $data['email'])->delete(); // invalidate previous by SoftDelete

        $expiration = (new DateTime())->modify('+' . config('constants.user.invitation_lifetime') . ' minutes');

        $invitation = new UserInvitation([
            'email' => $data['email'],
            'role_id' => Role::where('name', $data['role'])->first()->id,
            'expires_at' => $expiration
        ]);

        $invitation->save();
        return $invitation;
    }
}
