<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;

class ForgetPassswordRequest extends CustomFormRequest
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
        $this->lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';

        return $this->getValidationRules();
    }

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
        ];
    }

    /**
     * Get the validation rules for phone login.
     */
    protected function getPhoneValidationRules(): array
    {
        return [
            'phone' => 'required|exists:users,phone',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        $lang = $this->lang;

        return [
            'email.required' => __('validation.required.email', [], $lang),
            'email.exists' => __('validation.exists.email', [], $lang),
            'phone.required' => __('validation.required.phone', [], $lang),
            'phone.exists' => __('validation.exists.phone', [], $lang),
        ];
    }
}
