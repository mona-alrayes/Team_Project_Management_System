<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Load the task and the related project with its users in one query
        $task = Task::with('project.users')->find($this->input('task_id'));
        // If the task or project does not exist, deny access
        if (!$task || !$task->project) {
            return false;
        }

        // Get the specific project that this task belongs to
        $project = $task->project;
        // Find the authenticated user's role in the project's pivot table
        $userProject = $project->users()
            ->where('users.id', Auth::id()) // Avoid ambiguity by specifying the 'users' table
            ->first();
        // Check if the user exists in the project and get the role from the pivot table
        $userProjectRole = $userProject ? $userProject->pivot->role : null;
        // Only allow users with the 'tester' role to store notes
        return $userProjectRole === 'tester';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'note' => ['required', 'string', 'max:5000'],
            'task_id' => ['required',  'exists:tasks,id'],
            'user_id' => ['required',  'exists:users,id'],
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
            'required' => 'حقل :attribute مطلوب',
            'string' => 'حقل :attribute يجب أن يكون نصًا وليس أي نوع آخر',
            'integer' => 'حقل :attribute يحب ان يكون رقما',
            'exists' => 'القيمة المحددة في حقل :attribute غير موجودة'
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
            'note' => 'الملاحظة',
            'task_id' => 'معرف المهمة',
            'user_id' => 'معرف المستخدم',
        ];
    }


    protected function prepareForValidation()
    {
        $task = Task::where('title', $this->input('task_id'))->first();
        $this->merge([
            'note' => ucwords(strtolower($this->input('note'))),
            'user_id' => Auth::id(),
            'task_id' => $task->id,
        ]);
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param \Illuminate\Contracts\Validation\Validator $validator
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'status'  => 'خطأ',
            'message' => 'فشلت عملية التحقق من صحة البيانات.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
