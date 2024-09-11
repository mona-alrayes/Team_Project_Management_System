<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ];
    }
    /**
     * Get the custom error messages for validation rules.
     *
     * This method returns an array of custom error messages for validation
     * rules. The array keys should correspond to the validation rule names,
     * and the values are the custom error messages.
     *
     * @return array<string, string> Array of custom error messages.
     */
    public function messages(): array
    {
        return [
            'required' => 'حقل :attribute مطلوب ',
            'string' => 'حقل :attribute يجب أن يكون نصا وليس اي نوع اخر',
            'max' => 'عدد محارف :attribute لا يجب ان تتجاوز 255 محرفا',
            'email.required' => 'حقل :attribute مطلوب لا يمكن ان يكون فارغا',
            'email' => 'حقل :attribute يجب ان يكون بصيغة صحيحة مثل test@example.com',
            'email.unique' => 'هذا :attribute موجود بالفعل في بياناتنا',
            'min' => 'حقل :attribute يجب ان يكون 6 محارف على الاقل',
        ];
    }

    /**
     * Get the custom attribute names for validator errors.
     *
     * This method returns an array of custom attribute names that should
     * be used in error messages. The keys are the input field names, and
     * the values are the custom names to be used in error messages.
     *
     * @return array<string, string> Array of custom attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'الأسم',
            'email' => 'البريد الالكتروني',
            'password' => 'كلمة المرور',
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
        $this->merge([
            'name' => ucwords(strtolower($this->input('name'))),
        ]);
    }
    /**
     * Handle a failed validation attempt.
     *
     * This method is called when validation fails. It customizes the
     * response that is returned when validation fails, including the
     * status code and error messages.
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
