<?php

namespace App\Http\Resources\V1\Matching;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchingPaymentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            // 'matching' => new MatchingResource($this->matching),
            'stripeResponse' => $this->stripe_response,
        ];
    }
}
