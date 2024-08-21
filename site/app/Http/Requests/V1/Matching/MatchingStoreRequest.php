<?php

namespace App\Http\Requests\V1\Matching;

use App\Http\Requests\V1\CustomFormRequest;

class MatchingStoreRequest extends CustomFormRequest
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
                'job_id' => 'required|array|exists:jobs,id',
                // 'favourite' => 'required|boolean',
                // 'type'=>'required|in:create,favourite,matched,unmatched'
            ];
        } else {
            return [
                'job_seeker_id' => 'required|array|exists:jobseekers,id',
                // 'favourite' => 'required|boolean',
                // 'type'=>'required|in:create,favourite,matched,unmatched'
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
