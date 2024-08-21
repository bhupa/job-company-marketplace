<?php

namespace App\Http\Requests\V1\Matching;

use App\Http\Requests\V1\CustomFormRequest;

class MatchingPaymentRequest extends CustomFormRequest
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
            'matching_id' => 'required|exists:matching,id',
            'stripeToken' => 'required',
        ];
    }
}
