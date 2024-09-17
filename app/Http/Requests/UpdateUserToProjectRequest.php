<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserToProjectRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Adjust authorization as needed
    }

    public function rules(): array
    {
        return [
            'role' => 'nullable|string|in:manager,developer,tester',
        ];
    }

    public function attributes(): array
    {
        return [
            'role' => 'Role',
        ];
    }
}
