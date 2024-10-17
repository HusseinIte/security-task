<?php

namespace App\Http\Requests\User;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AssignUserRolesRequest extends FormRequest
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
            'role_ids' => 'required|array',
            'role_ids.*' => 'integer|exists:roles,id', // Validate each role ID
        ];
    }

    public function attributes()
    {
        return [
            'role_ids' => 'معرفات الأدوار',
            'role_ids.*' => 'معرف الدور'
        ];
    }
    public function messages()
    {
        return [
            'required' => ':attribute مطلوب',
            'array' => ':attribute مصفوفة',
            'integer' => ':attribute يجب أن يكون رقم',
            'exists' => ':attribute غير موجود'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors(), 'Validation Error', 422);
    }
}
