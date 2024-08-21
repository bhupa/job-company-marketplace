<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;
use Illuminate\Support\Facades\Hash;

class ChangePasswordRequest extends CustomFormRequest
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
        $user = auth()->user();
        if ($user->password) {
            return [
                'old_password' => [
                    'required',
                    function ($attribute, $value, $fail) use ($user) {
                        if (!Hash::check($value, $user->password)) {
                            $fail(
                                __(
                                    'validation.required.old_password_check',
                                    [],
                                    getUserLanguage(auth()->user())
                                )
                            );
                        }
                    },
                ],
                'password' => ['required', 'confirmed', 'min:8'],
            ];
        } else {
            return [
                'password' => ['required', 'confirmed', 'min:8'],
            ];
        }
    }

    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
            'old_password.required' => __(
                'validation.required.old_password',
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
