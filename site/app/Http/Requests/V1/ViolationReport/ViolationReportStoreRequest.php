<?php

namespace App\Http\Requests\V1\ViolationReport;

use App\Http\Requests\V1\CustomFormRequest;

class ViolationReportStoreRequest extends CustomFormRequest
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
            'user_id' => 'required_without:chat_room_id|exists:users,id',
            'chat_room_id' => 'required_without:user_id|exists:chat_room,id',
            // 'message'=>'required'
        ];
    }
}
