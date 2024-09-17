<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserToProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust authorization as needed
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string|in:manager,developer,tester',
        ];
    }

    public function attributes(): array
    {
        return [
            'user_id' => 'User ID',
            'role' => 'Role',
        ];
    }
}
