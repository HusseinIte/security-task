<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskPriority;
use App\Enums\TaskType;
use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
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
            'title'            => ['required', 'string', 'max:255'],
            'type'             => ['required', Rule::enum(TaskType::class)],
            'description'      => ['required', 'max:1000'],
            'priority'         => ['required', Rule::enum(TaskPriority::class)],
            'due_date'         => ['required', 'date', 'after_or_equal:today'],
            'assigned_to'      => ['required', 'integer', 'exists:users,id'],
            'dependency_ids'   => ['nullable', 'array'],
            'dependency_ids.*' => ['nullable', 'integer', 'exists:tasks,id']
        ];
    }
    public function attributes()
    {
        return [
            'title'            => 'اسم المهمة',
            'type'             => 'نوع المهمة',
            'description'      => 'وصف المهمة ',
            'status'           => 'حالة المهمة',
            'priority'         => 'الأولوية',
            'due_date'         => 'تاريح التسليم',
            'assigned_to'      => 'منفذ المهمة',
            'dependency_ids'   => 'معرفات تبعيات المهمة',
            'dependency_ids.*' => 'معرف التبعية '
        ];
    }

    public function messages()
    {
        return [
            'required'       => ':attribute مطلوب',
            'date'           => ':attribute غير صالح',
            'string'         => ':attribute نصي فقط',
            'integer'        => ':attribute أرقام فقط',
            'enum'           => ':attribute غير صالح',
            'exists'         => ':attribute غير موجود',
            'array'          => ':attribute مصفوفة فقط',
            'after_or_equal' => ':attribute غير صالح'
        ];
    }

    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors());
    }
}
