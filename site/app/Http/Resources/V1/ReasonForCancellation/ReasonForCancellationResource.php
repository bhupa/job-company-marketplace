<?php

namespace App\Http\Resources\V1\ReasonForCancellation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReasonForCancellationResource extends JsonResource
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
            'full_name'=>$this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'user_type' => ($this->user_type == 1) ? 'jobseeker' :'company',
            'reason' => $this->reason,
            'subReason' => $this->sub_reason,
            'futurePlan' => $this->future_plan,
            'comment' => $this->comment,
        ];
    }
}
