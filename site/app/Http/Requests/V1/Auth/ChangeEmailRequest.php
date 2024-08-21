<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;

class ChangeEmailRequest extends CustomFormRequest
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
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users',
                'email',
            ],
        ];
    }

    public function messages(): array
    {
        $lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';

        return [
            'email.required' => __('validation.required.email', [], $lang),
            'email.unique' => __('validation.unique.email', [], $lang),
        ];
    }
}
