<?php

namespace App\Http\Requests\V1\Auth;

use App\Http\Requests\V1\CustomFormRequest;

class VerifyAccountStoreRequest extends CustomFormRequest
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
            // 'images' => 'nullable|array|min:1|max:3',
            'images.*' => 'nullable|mimes:jpeg,jpg,png,gif,pdf|max:1048576',
            'remove' => [
                function ($attribute, $value, $fail) {
                    // Check if 'images' is empty and 'remove' is also empty
                    $imagesEmpty =
                        request()->has('images')
                        && empty(request()->input('images'));
                    $removeEmpty = empty($value);

                    if ($imagesEmpty && $removeEmpty) {
                        $fail(
                            "If 'images' is empty, 'remove' cannot be empty."
                        );
                    }
                },
            ],
            // Custom validation rule for allowing 3 images or 1 PDF or empty 'images' with 'remove' not empty
            'images' => [
                function ($attribute, $value, $fail) {
                    // Check if 'images' is not empty
                    if (!empty($value)) {
                        // Count number of images and PDFs
                        $numImages = 0;
                        $numPDFs = 0;
                        foreach ($value as $file) {
                            $extension = $file->getClientOriginalExtension();
                            if (
                                in_array($extension, [
                                    'jpeg',
                                    'jpg',
                                    'png',
                                    'gif',
                                ])
                            ) {
                                ++$numImages;
                            } elseif ($extension === 'pdf') {
                                ++$numPDFs;
                            }
                        }

                        // Check conditions
                        if ($numImages > 3 || $numPDFs > 1) {
                            $fail(
                                'You can upload a maximum of 3 images or 1 PDF.'
                            );
                        }
                    }
                },
            ],
        ];
    }
}
