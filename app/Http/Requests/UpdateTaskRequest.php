<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $projectID = $this->route('id'); // Get project ID from route
        $userID = Auth::id(); // Get the authenticated user ID
        $user = User::findOrFail($userID); // Find the authenticated user

        // Retrieve the user's role from the pivot table for the specific project
        $project = $user->projects()->where('project_id', $projectID)->first();

        return $project && $project->pivot->role === 'manager'; // Check if the user has a manager role
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'min:3', 'max:255'],
            'description' => ['sometimes', 'string', 'min:10', 'max:2000'],
            'priority' => ['sometimes', 'string', 'in:high,medium,low'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['sometimes', 'string', 'in:pending,in_progress,completed'],
            'due_date' => ['sometimes', 'date_format:d-m-Y H:i'],
        ];
    }

    /**
     * Get the custom error messages for the validator.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'string' => 'حقل :attribute يجب أن يكون نصا وليس أي نوع آخر',
            'max' => 'عدد محارف :attribute لا يجب أن يتجاوز 255 محرفا',
            'description.max' => 'لا يجب ان يتجاوز :attribute 2000 محرفا',
            'min' => 'حقل :attribute يجب أن يكون 3 محارف على الأقل',
            'description.min' => 'عدد محارف :attribute لا يقل عن 10 محارف',
            'priority.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: high, medium, low',
            'status.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: pending, in_progress, completed',
            'date_format' => 'حقل :attribute يجب أن يكون بصيغة تاريخ صحيحة مثل :format',
        ];
    }

    /**
     * Get custom attribute names for error messages.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'عنوان المهمة',
            'description' => 'الوصف',
            'priority' => 'الأولوية',
            'assigned_to' => 'المعين إلى',
            'status' => 'الحالة',
            'due_date' => 'تاريخ الاستحقاق',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->filled('assigned_to')) {
            $user = User::where('name', $this->input('assigned_to'))->first();
            if (!$user) {
                throw new HttpResponseException(response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed.',
                    'errors' => ['assigned_to' => ['User not found']],
                ], 422));
            }
            $this->merge(['assigned_to' => $user->id]);
        }

        $this->merge([
            'title' => ucwords(strtolower($this->input('title', ''))),
            'description' => ucwords(strtolower($this->input('description', ''))),
        ]);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
