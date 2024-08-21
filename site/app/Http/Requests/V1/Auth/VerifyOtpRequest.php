<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;

class VerifyOtpRequest extends CustomFormRequest
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
        // return [
        //     'otp' => 'required|exists:users,otp',
        // ];
    }

    protected function getValidationRules(): array
    {
        return $this->getOtpValidationRules();
    }

    public function getOtpValidationRules(): array
    {
        return [
            'otp' => 'required|exists:users,otp',
        ];
    }

    public function messages(): array
    {
        $lang = $this->lang;

        return [
            'otp.required' => __('validation.required.otp', [], $lang),
            'otp.exists' => __('validation.exists.otp', [], $lang),
        ];
    }
}
