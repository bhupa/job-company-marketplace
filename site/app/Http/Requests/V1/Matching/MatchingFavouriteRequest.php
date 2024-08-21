<?php

namespace App\Http\Requests\V1\Matching;

use App\Http\Requests\V1\CustomFormRequest;

class MatchingFavouriteRequest extends CustomFormRequest
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
                'favourite' => ['required', 'integer', 'in:0,1'],
            ];
        } else {
            return [
                'job_seeker_id' => 'required|exists:jobseekers,id',
                'favourite' => ['required', 'integer', 'in:0,1'],
            ];
        }
    }
}
