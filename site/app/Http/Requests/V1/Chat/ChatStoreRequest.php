<?php

namespace App\Http\Requests\V1\Chat;

use App\Http\Requests\V1\CustomFormRequest;

class ChatStoreRequest extends CustomFormRequest
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
            'chat_room_id' => 'required|exists:chat_room,id',
            'payment_id' => 'nullable|exists:payment,id',
        ];
    }

    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
            'type.required' => __('validation.required', [], $lang),
            'type.in' => __('validation.in', [], $lang),
            'message.required_unless' => __(
                'validation.required_unless',
                [],
                $lang
            ),
            'file.required_if' => __('validation.required_if', [], $lang),
            'chat_room_id.required' => __('validation.required', [], $lang),
            'chat_room_id.exists' => __('validation.exists', [], $lang),
            'payment_id.exists' => __('validation.exists', [], $lang), // Note: 'payment_id' is nullable, so no 'required' message
        ];
    }
}
