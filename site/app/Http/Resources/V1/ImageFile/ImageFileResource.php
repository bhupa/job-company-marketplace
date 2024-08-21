<?php

namespace App\Http\Resources\V1\ImageFile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageFileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request)
    {
        return [
            'id' => (string) $this->id,
            'fileType' => $this->file_type,
            'image' => url('/').'/storage/'.$this->image,
        ];
    }
}
