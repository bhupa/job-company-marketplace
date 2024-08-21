<?php

namespace App\Http\Resources\V1\Subscription;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanDetailsResource extends JsonResource
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
            'order' => $this->order,
            'name' => $this->name,
            'duration' => $this->description,
            'features' => $this->features,
            'price' => [
                'ja' => $this->price_jp,
                'en' => $this->price_usd,
                'npr' => $this->price_npr,
            ],
        ];
    }
}
