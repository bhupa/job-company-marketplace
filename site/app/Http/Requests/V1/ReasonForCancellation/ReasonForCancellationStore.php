<?php

namespace App\Http\Requests\V1\ReasonForCancellation;

use App\Http\Requests\V1\CustomFormRequest;

class ReasonForCancellationStore extends CustomFormRequest
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
        return [
            'reason' => 'required',
            'sub_reason' => 'nullable',
        ];
    }

    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
            'reason.required' => __(
                'validation.required.cancel_reason',
                [],
                $lang
            ),
        ];
    }
}
