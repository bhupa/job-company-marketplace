<?php

namespace App\Http\Requests\V1\DeviceToken;

use App\Http\Requests\V1\CustomFormRequest;

class DeviceTokenRequest extends CustomFormRequest
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
            'device_token' => 'required',
        ];
    }
}
