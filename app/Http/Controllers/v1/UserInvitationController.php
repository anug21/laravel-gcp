<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserInvitationRequest;
use App\Models\UserInvitation;
use App\Services\UserInvitationService;
use App\Traits\HttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UserInvitationController extends Controller
{
    use HttpResponse;

    public function store(UserInvitationRequest $request, UserInvitationService $userInvitationService): JsonResponse
    {
        $invitation = $userInvitationService->create($request->safe()->toArray());

        return $this->response(
            [],
            __('messages.invitation.created', [
                'email' => $invitation->email,
                'expiration' => $invitation->expires_at->format('r')
            ])
        );
    }

    public function verify(Request $request): RedirectResponse
    {

        $url = config('app.frontend_url');
        $pathSuccess = config('frontend.invitation_success_redirect');
        $pathFail = config('frontend.invitation_fail_redirect');

        $invitation = UserInvitation::where('signature', $request->signature)
            ->where('expires_at', '>=', now())->first();

        if (is_null($invitation)) {
            return redirect($url . $pathFail);
        }

        $param = Arr::query(['signature' => $invitation->signature]);

        return redirect($url . $pathSuccess . "?$param");
    }
}
