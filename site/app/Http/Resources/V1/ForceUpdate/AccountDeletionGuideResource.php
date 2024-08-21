<?php

namespace App\Http\Resources\V1\ForceUpdate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountDeletionGuideResource extends JsonResource
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
            'content' => [
                'en' => $this->en_content,
                'ja' => $this->ja_content,
            ],
        ];
    }
}
