<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationRequest;
use App\Services\UserInvitationService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;

class UserInvitationController extends Controller
{
    use HttpResponse;

    public function store(UserInvitationRequest $request): JsonResponse
    {
        $invitation = (new UserInvitationService())->create($request->safe()->toArray());

        return $this->response(
            [],
            __('messages.user.invitation_created', [
                'email' => $invitation->email,
                'expiration' => $invitation->expires_at->format('r')
            ])
        );
    }
}
