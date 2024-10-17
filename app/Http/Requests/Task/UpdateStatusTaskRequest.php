<?php

namespace App\Http\Requests\Task;

use App\Enums\TaskStatus;
use App\Exceptions\AuthorizationException;
use App\Exceptions\ValidationException;
use App\Models\Task;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UpdateStatusTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $taskId = $this->route('id');
        $task = Task::find($taskId);
        $checkUser =  Auth::user()->id == $task->assigned_to;
        if (!$checkUser) {
            throw new AuthorizationException("This action is unauthorized.");
        }
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
            'status' => ['required', Rule::enum(TaskStatus::class)]
        ];
    }
    public function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors());
    }
}
