<?php

namespace App\Http\Requests\V1\Company;

use App\Http\Requests\V1\CustomFormRequest;

class CompanyUpdateRequest extends CustomFormRequest
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
        $company = $this->route('company');

        return [
            'company_name' => 'nullable',
            'about_company' => 'nullable',
            'about_company_ja' => 'nullable',
            'address' => 'nullable',
            'image' => 'nullable|array|max:1048576',
            'logo' => 'nullable',
            'intro_video' => 'nullable|file|mimetypes:video/*',
        ];
    }

    public function messages(): array
    {
        $lang = getUserLanguage(auth()->user());

        return [
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
