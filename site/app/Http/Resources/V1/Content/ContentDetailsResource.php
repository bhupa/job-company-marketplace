<?php

namespace App\Http\Resources\V1\Content;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContentDetailsResource extends JsonResource
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
            'title' => $this->title_en,
            'type' => $this->type,
            'link' => $this->link === 'Null' ? '' : $this->link,
            'description' => $this->description_en,
        ];
    }
}
