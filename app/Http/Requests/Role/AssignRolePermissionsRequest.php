<?php

namespace App\Http\Requests\Role;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AssignRolePermissionsRequest extends FormRequest
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
            'permission_ids' => 'required|array',
            'permission_ids.*' => 'integer|exists:permissions,id', // Validate each permission ID
        ];
    }

    public function attributes()
    {
        return [
            'permission_ids' => 'معرفات الصلاحيات',
            'permission_ids.*' => 'معرف الصلاحية'
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
