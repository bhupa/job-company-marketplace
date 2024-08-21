<?php

namespace App\Http\Requests\V1\Jobseeker;

use App\Http\Requests\V1\CustomFormRequest;

class JobseekerUpdateRequest extends CustomFormRequest
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
            'first_name' => 'nullable',
            'last_name' => 'nullable',
            'image' => 'nullable|array',
            'birthday' => 'nullable',
            'gender' => 'nullable|integer|min:1|max:4|not_in:5', // male = 1 , female = 2, binary = 3
            'country' => 'nullable',
            'current_country' => 'nullable',
            'occupation' => 'nullable|exists:jobs_category,id',
            'job_type' => 'nullable|integer|min:1|max:8|not_in:9',
            'experience' => 'nullable|integer|min:0|max:4|not_in:5', // less than 1 year = 1, less than 2 year =2, less than 3 year = 3, 3 or more = 4
            'japanese_level' => 'nullable|integer|min:0|max:5|not_in:6', // N1 = 1 , N2 = 2, N3 = 3, N4 = 4 , N5 =5
            'about' => 'nullable',
            'about_ja' => 'nullable',
            'living_japan' => 'nullable|boolean',
            'start_when' => 'nullable',
            'intro_video' => 'nullable|file|mimetypes:video/*',
        ];
    }

    public function messages(): array
    {
        $lang = request()->header('Accept-Language')
            ? request()->header('Accept-Language')
            : 'en';

        return [
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
