<?php

namespace App\Http\Resources\V1\Chat;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'isDeleted' => $this->send_by ? false : true,
            'send_by' => [
                'userId' => $this->send_by ? (string) $this->send_by : null,
                'userType' => $this->send_by ? $this->sendBy->user_type : null,
                'id' => $this->send_by
                    ? ($this->sendBy->user_type == 1
                        ? (string) $this->sendBy->jobseeker->id
                        : (string) $this->sendBy->company->id)
                    : null,
                'name' => $this->send_by
                    ? ($this->sendBy->user_type == 1
                        ? $this->sendBy->jobseeker->first_name.
                            ' '.
                            $this->sendBy->jobseeker->last_name
                        : $this->sendBy->company->company_name)
                    : null,
                'image' => $this->send_by
                    ? ($this->sendBy->user_type == 1
                        ? ($this->sendBy->jobseeker->profile_img
                            ? url('/').
                                '/storage/'.
                                $this->sendBy->jobseeker->profile_img
                            : 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU')
                        : ($this->sendBy->company->logo
                            ? url('/').
                                '/storage/'.
                                $this->sendBy->company->logo
                            : 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU'))
                    : null,
            ],
            'message' => $this->message,
            'seen' => $this->seen
                ? Carbon::parse($this->seen)->toIso8601ZuluString()
                : null,
            'admin_id' => $this->admin_id ? $this->admin : null,
            'room' => (string) $this->chat_room_id,
            'createdAt' => $this->created_at,
        ];
    }
}
