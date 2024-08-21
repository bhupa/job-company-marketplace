<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;

class ResetPasswordRequest extends CustomFormRequest
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
            'user_id' => 'required:exists:users,id',
            'password' => ['required', 'confirmed', 'min:8'],
        ];
    }

    public function messages(): array
    {
        $lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';

        return [
            'user_id.required' => __(
                'validation.required.user.required',
                [],
                $lang
            ),
            'user_id.exists' => __(
                'validation.required.user.exxists',
                [],
                $lang
            ),

            'password.required' => __(
                'validation.required.password',
                [],
                $lang
            ),
            'password.confirmed' => __('validation.confirmed', [], $lang),
            'password.min' => __('validation.min.password', [], $lang),
        ];
    }
}
