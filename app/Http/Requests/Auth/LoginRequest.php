<?php

namespace App\Http\Requests\Auth;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
            'email' => 'required|string|email|max:30',
            'password' => 'required|string|min:8|max:30'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors(), 'Validation Error', 422);
    }
}
