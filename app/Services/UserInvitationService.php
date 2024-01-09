<?php

namespace App\Services;

use App\Models\UserInvitation;
use App\Notifications\UserInvitationCreated;
use Carbon\Carbon;
use DateTime;
use Spatie\Permission\Models\Role;
use Str;

class UserInvitationService
{
    public function create(array $data): UserInvitation
    {
        UserInvitation::where('email', $data['email'])->delete(); // invalidate previous by SoftDelete

        $expiration = (new DateTime())->modify('+' . config('constants.user.invitation_lifetime') . ' minutes');
        $signature = bin2hex(openssl_random_pseudo_bytes(32));

        $invitation = new UserInvitation([
            'email' => $data['email'],
            'role_id' => Role::where('name', $data['role'])->first()->id,
            'expires_at' => $expiration,
            'signature' => $signature,
        ]);

        $invitation->save();

        $invitation->notify(
            new UserInvitationCreated(
                auth()->user()->fullName,
                Str::headline($data['role'])
            )
        );

        return $invitation;
    }

    public function getBySignature(string $signature): ?UserInvitation
    {
        return UserInvitation::where('signature', $signature)
            ->where('expires_at', '>=', Carbon::now())
            ->first();
    }

    public function invalidateAndFetchEmail(string $signature): ?string
    {
        $invitation = $this->getBySignature($signature);

        if (is_null($invitation)) {
            return null;
        }

        $email = $invitation->email;
        $invitation->delete();
        return $email;
    }
}
