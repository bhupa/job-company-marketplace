<?php

namespace App\Http\Requests\V1\Matching;

use App\Http\Requests\V1\CustomFormRequest;

class UnRewindRequest extends CustomFormRequest
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
        if (auth()->user()->user_type === 1) {
            return [
                'job_id' => 'required|exists:jobs,id',
            ];
        } else {
            return [
                'job_seeker_id' => 'required|exists:jobseekers,id',
            ];
        }
    }

    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
            'job_id.required' => __(
                'validation.required.job.required',
                [],
                $lang
            ),
            'job_id.exists' => __('validation.required.job.exists', [], $lang),
            'job_seeker_id.required' => __(
                'validation.required.jobseeker.required',
                [],
                $lang
            ),
            'job_seeker_id.exists' => __(
                'validation.required.jobseeker.exists',
                [],
                $lang
            ),
        ];
    }
}
