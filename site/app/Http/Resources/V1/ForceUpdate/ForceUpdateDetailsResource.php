<?php

namespace App\Http\Resources\V1\ForceUpdate;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ForceUpdateDetailsResource extends JsonResource
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
            'ios' => [
                'version' => $this->ios_version,
                'buildNumber' => $this->ios_build_number,
            ],
            'android' => [
                'version' => $this->android_version,
                'buildNumber' => $this->android_build_number,
            ],
        ];
    }
}
