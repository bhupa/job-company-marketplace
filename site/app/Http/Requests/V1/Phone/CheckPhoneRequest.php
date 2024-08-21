<?php

namespace App\Http\Requests\V1\Phone;

use App\Http\Requests\V1\CustomFormRequest;

class CheckPhoneRequest extends CustomFormRequest
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
        if ($this->has('phone')) {
            return [
                'country_code' => 'required',
                'phone' => 'required',
            ];
        } else {
            return [
                'email' => 'required',
            ];
        }
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
        ];
    }
}
