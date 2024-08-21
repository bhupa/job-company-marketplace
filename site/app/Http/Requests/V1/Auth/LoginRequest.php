<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;

class LoginRequest extends CustomFormRequest
{
    protected $lang;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->getValidationRules();
    }

    /**
     * Get the validation rules based on the request data.
     */
    protected function getValidationRules(): array
    {
        if (
            $this->request->has('email')
            && !empty($this->request->get('email'))
        ) {
            return $this->getEmailValidationRules();
        } else {
            return $this->getPhoneValidationRules();
        }
    }

    /**
     * Get the validation rules for email login.
     */
    protected function getEmailValidationRules(): array
    {
        return [
            'email' => 'required|email|exists:users,email',
            'password' => 'required',
        ];
    }

    /**
     * Get the validation rules for phone login.
     */
    protected function getPhoneValidationRules(): array
    {
        return [
            'phone' => 'required|exists:users,phone',
            'password' => 'required',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        $lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';

        return [
            'email.required' => __('validation.required.email', [], $lang),
            'email.exists' => __('validation.exists.email', [], $lang),
            'phone.required' => __('validation.required.phone', [], $lang),
            'phone.exists' => __('validation.exists.phone', [], $lang),
            'password.required' => __(
                'validation.required.password',
                [],
                $lang
            ),
        ];
    }
}
