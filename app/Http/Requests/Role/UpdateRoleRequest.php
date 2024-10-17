<?php

namespace App\Http\Requests\Role;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;


class UpdateRoleRequest extends FormRequest
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
        $roleId = $this->route('role');
        return [
            'name' => 'sometimes|required|string|max:25|unique:roles,name,' . $roleId,
            'description' => 'nullable|string|max:255'
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'اسم الدور',
            'desription' => 'وصف الدور'
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
