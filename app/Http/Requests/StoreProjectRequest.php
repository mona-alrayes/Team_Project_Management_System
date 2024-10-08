<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string' , 'min:3', 'max:255' , 'unique:projects,id'],
            'description' => ['required', 'string', 'min:10', 'max:5000'],
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
            'unique' => 'حقل :arrtibute موجود من قبل',
            'required' => 'حقل :attribute مطلوب',
            'string' => 'حقل :attribute يجب أن يكون نصًا وليس أي نوع آخر',
            'max' => 'عدد محارف :attribute لا يجب أن يتجاوز 255 محرفًا',
            'description.max' => 'لا يجب أن يتجاوز :attribute 5000 محرفًا',
            'min' => 'حقل :attribute يجب أن يكون 3 محارف على الأقل',
            'description.min' => 'عدد محارف :attribute لا يقل عن 10 محارف',
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
            'name' => 'المشروع',
            'description' => 'الوصف',
        ];
    }

    protected function prepareForValidation()
    {
        
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
