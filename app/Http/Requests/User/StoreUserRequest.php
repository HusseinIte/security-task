<?php

namespace App\Http\Requests\User;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',

        ];
    }
    public function attributes()
    {
        return [
            'name' => 'اسم المستخدم',
            'email' => 'البريد الإلكتروني',
            'password' => 'كلمة السر ',
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute مطلوب',
            'string' => ':attribute محارف فقط',
            'email' => ':attribute غير صالح',
            'unique' => ':attribute موجود بالفعل',
            'confirmed' => ':attribute غير متطابقة',

        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors(), 'Validation Error', 422);

    }
}
