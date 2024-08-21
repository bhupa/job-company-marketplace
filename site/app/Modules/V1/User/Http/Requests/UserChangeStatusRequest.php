<?php

namespace App\Modules\V1\User\Http\Requests;

use App\Http\Requests\V1\CustomFormRequest;

class UserChangeStatusRequest extends CustomFormRequest
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
            'status' => 'required|in:1,2,3',
        ];
    }
}