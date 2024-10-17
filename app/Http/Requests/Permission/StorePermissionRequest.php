<?php

namespace App\Http\Requests\Permission;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
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
            'name' => 'required|string|max:25|unique:permissions,name',
            'description' => 'nullable|string|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'اسم الصلاحية',
            'desription' => 'وصف الصلاحية'
        ];
    }

    public function messages()
    {
        return [
            'required' => ':attribute مطلوب',
            'string' => ':attribute محارف نصية فقط',
            'unique' => ':attribute موجود بالفعل'

        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors(), 'Validation Error', 422);
    }
}
