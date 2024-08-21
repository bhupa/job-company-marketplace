<?php

namespace App\Http\Resources\V1\JobLocation;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DistrictDetailsResource extends JsonResource
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
            'label' => $this->name,
            'label_ja' => $this->ja_name,
            'value' => (string) $this->id,
            'isSectionHeader' => $this->parent_id == null ? true : false,
            'sections' => DistrictDetailsResource::collection($this->child),
        ];
    }
}
