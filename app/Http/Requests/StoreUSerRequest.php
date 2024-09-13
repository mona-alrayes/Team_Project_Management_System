<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreUSerRequest extends FormRequest
{/**
     * Determine if the user is authorized to make this request.
     *
     * This method determines if the user making the request is authorized
     * to perform this action. By default, it returns true, allowing all
     * requests to pass authorization. Override this method to implement
     * custom authorization logic.
     *
     * @return bool True if authorized, otherwise false.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * This method returns an array of validation rules that apply to the
     * request. Each key in the array represents an input field, and each
     * value is an array of validation rules.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string> Array of validation rules.
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'system_role' => ['nullable', 'in:admin,user'],
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
            'min' => 'حقل :attribute يجب ان يكون 8 محارف على الاقل',
            'password.confirmed' => 'حقل تأكيد :attribute غير مطابق لحقل :attribute',
            'system_role.in' => 'حقل :attribute يجب أن يكون واحدًا من القيم التالية: admin, user',
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
            'system_role' => 'صلاحية النظام'
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
            'status'  => 'error',
            'message' => 'Validation failed.',
            'errors'  => $validator->errors(),
        ], 422));
    }
}