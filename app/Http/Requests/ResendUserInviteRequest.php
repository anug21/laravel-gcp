<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ResendUserInviteRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('id')
        ]);
    }

    public function rules(): array
    {
        return [
            'id' => ['required', 'integer', 'exists:user_invitations,id']
        ];
    }
}
