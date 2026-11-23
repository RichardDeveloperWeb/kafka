<?php

namespace App\Http\Requests\Admin;

use Illuminate\Validation\Rules\Password;

class StoreUserRequest extends SaveUserRequest
{
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}
