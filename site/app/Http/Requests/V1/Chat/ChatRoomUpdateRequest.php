<?php

namespace App\Http\Requests\V1\Chat;

use App\Http\Requests\V1\CustomFormRequest;

class ChatRoomUpdateRequest extends CustomFormRequest
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
            'name' => 'nullable|string',
            'image' => 'nullable|image',
            'status' => 'nullable|boolean',
            'admin_assist' => 'nullable|boolean',
        ];
    }
}
