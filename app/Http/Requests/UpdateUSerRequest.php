<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateUSerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true; // Authorization logic can be added here if needed
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->route('user'); // Assume 'user' is the route parameter name for user ID

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email,'  . $userId],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'system_role' => ['nullable', 'in:admin,user'],
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
            'email' => 'حقل :attribute يجب أن يكون بصيغة صحيحة مثل test@example.com',
            'email.unique' => 'هذا :attribute موجود بالفعل في بياناتنا',
            'min' => 'حقل :attribute يجب أن يكون 8 محارف على الأقل',
            'password.confirmed' => 'حقل تأكيد :attribute غير مطابق لحقل :attribute',
            'system_role.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: admin,user',
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
            'name' => 'الأسم',
            'email' => 'البريد الالكتروني',
            'password' => 'كلمة المرور',
            'role' => 'الصلاحية',
        ];
    }

    /**
     * Handle actions to be performed before validation passes.
     *
     * This method is called before validation performed . You can use this
     * method to modify the request data before it is processed by the controller.
     *
     * For example, you might want to format or modify the input data.
     */
    protected function prepareForValidation()
    {
        if ($this->has('name')) {
            $this->merge([
                'name' => ucwords(strtolower($this->input('name'))),
            ]);
         } 
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
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}
