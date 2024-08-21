<?php

namespace App\Modules\V1\Auth\Http\Requests;

use App\Http\Requests\V1\CustomFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends CustomFormRequest
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
        return [
            'email' => [
                'required',
                'string',
                'max:255',
                'unique:users',
                'email',
            ],
            'country_code' => ['required'],
            'phone' => ['required', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
            'user_type' => ['required', 'integer', 'in:1,2'],
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
            'phone.required' => __('validation.required.phone', [], $lang),
            'country_code.required' => __(
                'validation.required.country_code',
                [],
                $lang
            ),
            'phone.unique' => __('validation.unique.phone', [], $lang),
            'password.required' => __(
                'validation.required.password',
                [],
                $lang
            ),
            'password.confirmed' => __('validation.confirmed', [], $lang),
            'password.min' => __('validation.min.password', [], $lang),
            'user_type.required' => __(
                'validation.required.user_type',
                [],
                $lang
            ),
            'user_type.integer' => __(
                'validation.integer.user_type',
                [],
                $lang
            ),
            'user_type.in' => __('validation.in.user_type', [], $lang),
        ];
        }
}