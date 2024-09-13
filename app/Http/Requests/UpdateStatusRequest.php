<?php

namespace App\Http\Requests;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use PHPOpenSourceSaver\JWTAuth\Contracts\Providers\Auth;

class UpdateStatusRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Get the task ID from the route 
        $taskId = $this->route('id');
        // Retrieve the task from the database
        $task = Task::find($taskId);
        // If the task does not exist, you can throw a 404 error or return false
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'status' => 'خطأ',
                'message' => 'المهمة غير موجودة',
            ], 404));
        }

        // Check if the task exists and the authenticated user is the one assigned to the task
        return $task && $task->assigned_to === auth()->user()->user_id; 
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'status' => ['required','string','in:in_progress,completed'],
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
            'string' => 'حقل :attribute يجب ان يكون نصا',
            'status.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: progress, Done', 
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
            'status' => 'الحالة',
        ];
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
            'message' => 'فشلت المصادقة',
            'errors'  => $validator->errors(),
        ], 422));
    }
}