<?php

namespace App\Http\Requests\V1\Chat;

use App\Http\Requests\V1\CustomFormRequest;

class DirectChatRequest extends CustomFormRequest
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
            'type' => 'required|in:text,file',
            'message' => 'required_unless:type,file',
            'file' => 'required_if:type,file',
            'user_id' => 'required|exists:users,id',
            'payment_id' => 'nullable|exists:payment,id',
        ];
    }
}
