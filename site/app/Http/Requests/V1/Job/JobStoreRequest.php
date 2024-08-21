<?php

namespace App\Http\Requests\V1\Job;

use App\Http\Requests\V1\CustomFormRequest;

class JobStoreRequest extends CustomFormRequest
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
            'job_title' => 'required',
            'occupation' => 'required|exists:jobs_category,id',
            'job_location' => 'required',
            'salary_from' => 'required',
            'salary_to' => 'nullable',
            'working_hours' => 'nullable',
            'gender' => 'nullable|integer|min:1|max:4|not_in:5', // male = 1 , female = 2, binary = 3
            'experience' => 'nullable|integer|min:0|max:4|not_in:5', // less than 1 year = 1, less than 2 year =2, less than 3 year = 3, 3 or more = 4
            'japanese_level' => 'nullable|integer|min:0|max:5|not_in:6', // N1 = 1 , N2 = 2, N3 = 3, N4 = 4 , N5 =5
            'required_skills' => 'required',
            'from_when' => 'nullable',
            'job_type' => 'nullable|integer|min:1|max:8|not_in:9',
            'pay_type' => 'required|in:hour,day,month,year,outsourcing',
            'tags' => 'nullable|array|exists:tags,id',
        ];
    }

    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
            'tags.array' => __('validation.required.tags.array', [], $lang),
            'tags.exists' => __('validation.required.tags.exists', [], $lang),
            'job_title.required' => __(
                'validation.required.job.title',
                [],
                $lang
            ),
            'salary_from.required' => __(
                'validation.required.job.salary_from',
                [],
                $lang
            ),
            'required_skills.required' => __(
                'validation.required.job.required_skills',
                [],
                $lang
            ),
            'job_type.required' => __(
                'validation.required.job_type',
                [],
                $lang
            ),
            'job_location.required' => __(
                'validation.required.job.location.required',
                [],
                $lang
            ),
            'job_location.exists' => __(
                'validation.required.job.location.exists',
                [],
                $lang
            ),
            'occupation.required' => __(
                'validation.required.job.occupation.required',
                [],
                $lang
            ),
            'occupation.exists' => __(
                'validation.required.job.occupation.exists',
                [],
                $lang
            ),
            'job_type.max' => __('validation.required.job_type', [], $lang),
            'job_type.min' => __('validation.required.job_type', [], $lang),
            'pay_type.required' => __(
                'validation.required.pay_type',
                [],
                $lang
            ),
            'pay_type.in' => __(
                'validation.required.pay_type_in',
                [],
                $lang
            ),
            'experience_required.boolean' => __(
                'validation.boolean',
                [],
                $lang
            ),
            'gender.integer' => __(
                'validation.required.gender.integer',
                [],
                $lang
            ),
            'gender.min' => __('validation.required.gender.min', [], $lang),
            'gender.max' => __('validation.required.gender.max', [], $lang),
            'japanese_level.integer' => __(
                'validation.required.japanese_level.integer',
                [],
                $lang
            ),
            'japanese_level.min' => __(
                'validation.required.japanese_level.min',
                [],
                $lang
            ),
            'japanese_level.max' => __(
                'validation.required.japanese_level.max',
                [],
                $lang
            ),
            'experience.integer' => __(
                'validation.required.experience.integer',
                [],
                $lang
            ),
            'experience.min' => __(
                'validation.required.experience.min',
                [],
                $lang
            ),
            'experience.max' => __(
                'validation.required.experience.max',
                [],
                $lang
            ),
        ];
    }
}
