<?php

namespace App\Modules\V1\SocialLogin\Http\Requests;

use App\Http\Requests\V1\CustomFormRequest;

class SocialLoginRequest extends CustomFormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'provider' => 'required',
            'token' => 'required',
            'user_type' => ['required', 'integer', 'in:1,2'],
        ];
    }
}