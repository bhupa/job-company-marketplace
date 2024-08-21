<?php

namespace App\Modules\V1\Company\Http\Requests;

use App\Http\Requests\V1\CustomFormRequest;

class CompanyStoreRequest extends CustomFormRequest
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
     */
    public function rules(): array
    {
        return [
            'company_name' => 'required',
            'tags' => 'nullable|array|exists:tags,id',
            'about_company' => 'required',
            'about_company_ja' => 'required',
            'intro_video' => 'nullable|file|mimetypes:video/*',
            'address' => 'required',
            'image' => 'nullable|array',
            'logo' => 'nullable',
            'job' => 'required|array',
            'job.job_title' => 'required',
            'job.occupation' => 'required|exists:jobs_category,id',
            'job.job_location' => 'required|exists:districts,id',
            'job.salary_from' => 'required',
            'job.salary_to' => 'nullable',
            'job.gender' => 'nullable|integer|min:1|max:2', // male = 1 , female = 2, binary = 3
            'job.experience' => 'nullable|integer|min:0|max:4|not_in:5', // less than 1 year = 1, less than 2 year =2, less than 3 year = 3, 3 or more = 4
            'job.japanese_level' => 'nullable|integer|min:0|max:5|not_in:6', // N1 = 1 , N2 = 2, N3 = 3, N4 = 4 , N5 =5
            'job.required_skills' => 'required',
            'job.from_when' => 'nullable',
            'job.job_type' => 'nullable|integer|min:1|max:8',
            'job.pay_type' => 'required|in:hour,day,month,year,outsourcing',
        ];
    }

    /**
     * Get the validation rules based on the request data.
     */

    /**
     * Get the validation rules for email login.
     */

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
            'company_name.required' => __(
                'validation.required.company.name',
                [],
                $lang
            ),
            'about_company.required' => __(
                'validation.required.company.about',
                [],
                $lang
            ),
            'tags.exists' => __('validation.required.tags.exists', [], $lang),
            'address.required' => __(
                'validation.required.company.address',
                [],
                $lang
            ),
            'job.required' => __('validation.required.company.job', [], $lang),
            'job.array' => __('validation.required.array', [], $lang),
            'job.job_title.required' => __(
                'validation.required.job.title',
                [],
                $lang
            ),
            'job.salary_from.required' => __(
                'validation.required.job.salary_from',
                [],
                $lang
            ),
            'job.required_skills.required' => __(
                'validation.required.job.required_skills',
                [],
                $lang
            ),
            'job.job_type.required' => __(
                'validation.required.job_type',
                [],
                $lang
            ),
            'job.pay_type.required' => __(
                'validation.required.pay_type',
                [],
                $lang
            ),
            'job.pay_type.in' => __(
                'validation.required.pay_type_in',
                [],
                $lang
            ),
            'job.job_location.required' => __(
                'validation.required.job.location.required',
                [],
                $lang
            ),
            'job.job_location.exists' => __(
                'validation.required.job.location.exists',
                [],
                $lang
            ),
            'job.occupation.required' => __(
                'validation.required.job.occupation.required',
                [],
                $lang
            ),
            'job.occupation.exists' => __(
                'validation.required.job.occupation.exists',
                [],
                $lang
            ),
            'job.job_type.max' => __('validation.required.job_type', [], $lang),
            'job.job_type.min' => __('validation.required.job_type', [], $lang),

            'job.temporary_staff.boolean' => __(
                'validation.boolean',
                [],
                $lang
            ),
            'job.gender.integer' => __(
                'validation.required.gender.integer',
                [],
                $lang
            ),
            'job.gender.min' => __('validation.required.gender.min', [], $lang),
            'job.gender.max' => __('validation.required.gender.max', [], $lang),
            'job.japanese_level.integer' => __(
                'validation.required.japanese_level.integer',
                [],
                $lang
            ),
            'job.japanese_level.min' => __(
                'validation.required.japanese_level.min',
                [],
                $lang
            ),
            'job.japanese_level.max' => __(
                'validation.required.japanese_level.max',
                [],
                $lang
            ),
            'job.experience.integer' => __(
                'validation.required.experience.integer',
                [],
                $lang
            ),
            'job.experience.min' => __(
                'validation.required.experience.min',
                [],
                $lang
            ),
            'job.experience.max' => __(
                'validation.required.experience.max',
                [],
                $lang
            ),
            'intro_video.required' => __(
                'validation.required.intro_video',
                [],
                $lang
            ),
            'intro_video.mimetypes' => __(
                'validation.required.intro_video_file',
                [],
                $lang
            ),
        ];
    }
}
