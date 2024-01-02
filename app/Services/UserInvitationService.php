<?php

namespace App\Services;

use App\Mail\UserInvitationMail;
use App\Models\UserInvitation;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;

class UserInvitationService
{
    public static function create(array $data): UserInvitation
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

        Mail::to($data['email'])->send(new UserInvitationMail(['signature' => $signature]));

        return $invitation;
    }

    public static function getBySignature(string $signature): ?UserInvitation
    {
        return UserInvitation::where('signature', $signature)
            ->where('expires_at', '>=', Carbon::now())
            ->first();
    }

    public static function invalidateAndFetchEmail(string $signature): ?string
    {
        $invitation = self::getBySignature($signature);

        if (is_null($invitation)) {
            return null;
        }

        $email = $invitation->email;
        $invitation->delete();
        return $email;
    }
}
