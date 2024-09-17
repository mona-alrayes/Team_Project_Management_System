<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreTaskRequest extends FormRequest
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
            'title' => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'priority' => ['required', 'string', 'in:high,medium,low'],
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],
            'status' => ['nullable', 'string', 'in:pending,in_progress,completed'],
            'due_date' => ['required', 'date_format:d-m-Y H:i', 'after_or_equal:now'],
            'project_id' => ['required','exists:projects,id'],
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
            'max' => 'عدد محارف :attribute لا يجب أن يتجاوز 255 محرفًا',
            'description.max' => 'لا يجب أن يتجاوز :attribute 2000 محرفًا',
            'min' => 'حقل :attribute يجب أن يكون 3 محارف على الأقل',
            'description.min' => 'عدد محارف :attribute لا يقل عن 10 محارف',
            'priority.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: high, medium, low',
            'status.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: pending, in_progress, completed',
            'date_format' => 'حقل :attribute يجب أن يكون بصيغة تاريخ صحيحة مثل :format',
            'after_or_equal' => 'لا يمكن أن يكون :attribute تاريخًا في الماضي',
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
        // In input form user input the name of the person to assign the task to and here we get the object of this person to get the id of it 
        $user = User::where('name', $this->input('assigned_to'))->first();
       
        $this->merge([
            'title' => ucwords(strtolower($this->input('title'))),
            'assigned_to' => $user ? $user->id : null,
            'status' => 'pending',
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
            'status'  => 'خطأ',
            'message' => 'فشلت عملية التحقق من صحة البيانات.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
