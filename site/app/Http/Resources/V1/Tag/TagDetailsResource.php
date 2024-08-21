<?php

namespace App\Http\Resources\V1\Tag;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TagDetailsResource extends JsonResource
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
            'type' => $this->type,
            'value' => (string) $this->id,
        ];
    }
}
