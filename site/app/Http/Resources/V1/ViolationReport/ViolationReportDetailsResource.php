<?php

namespace App\Http\Resources\V1\ViolationReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ViolationReportDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'message' => $this->message,
            'user' => $this->user,
            'createdBy' => $this->createdBy,
            'chat' => $this->chatroom,
        ];
    }
}
