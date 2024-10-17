<?php

namespace App\Http\Requests\Attachment;

use App\Exceptions\ValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class StoreAttachmentRequest extends FormRequest
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
        $mimetypes = [
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/webp',
            'application/pdf',
            'application/msword',
            'audio/mpeg',
            'video/mp4',
        ];

        return [
            'file'     => ['required', 'file', 'mimetypes:' . implode(',', $mimetypes),  'max:51200'], // Max file size is 50MB
            'name'     => ['nullable', 'string', 'max:25'],
            'alt_text' => ['nullable', 'max:255']
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator->errors(), 'Validation Error', 422);
    }
}
