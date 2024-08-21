<?php

namespace App\Http\Resources\V1\Job;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobCategoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'value' => (string) $this->id,
            'label' => $this->name,
            'label_ja' => $this->jp_name,
        ];
    }
}
