<?php

namespace App\Http\Requests\Admin;

use App\Enums\ManagerRole;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

abstract class SaveUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $user = $this->route('user');
        $ignore = $user instanceof User ? $user->id : null;

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($ignore)],
            'phone' => ['nullable', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['required', 'string', Rule::in($this->roleValues())],
        ];
    }

    /**
     * @return list<string>
     */
    private function roleValues(): array
    {
        return [
            ...array_map(fn (UserRole $role): string => $role->value, UserRole::cases()),
            ...array_map(fn (ManagerRole $role): string => $role->value, ManagerRole::cases()),
        ];
    }
}
