<?php

namespace App\Http\Requests;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateNoteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the task and the associated project
        $task = Task::find($this->input('task_id'));

        if (!$task) {
            return false; // If the task doesn't exist, deny the request
        }

        // Get the user's role in the project through the pivot table
        $userProjectRole = $task->project->users()
            ->where('user_id', Auth::id())
            ->first()
            ->pivot->role ?? null;

        // Only allow users with the role 'tester' to update notes
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
            'note' => ['nullable', 'string', 'max:5000'],
            'task_id' => ['nullable', 'integer', 'exists:tasks,id'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
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
            'string' => 'حقل :attribute يجب أن يكون نصًا وليس أي نوع آخر',
            'integer' => 'حقل :attribute يحب ان يكون رقما',
            'exists' => 'القيمة المحددة في حقل :attribute غير موجودة',
            'max' => 'حقل :attribute يجب ألا يتجاوز :max حرفًا',
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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        $task = Task::find($this->input('task_id'));

        $this->merge([
            'note' => $this->input('note') ? ucwords(strtolower($this->input('note'))) : null,
            'user_id' => Auth::id(),
            'task_id' => $task ? $task->id : $this->input('task_id'),
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
            'status' => 'خطأ',
            'message' => 'فشلت عملية التحقق من صحة البيانات.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
