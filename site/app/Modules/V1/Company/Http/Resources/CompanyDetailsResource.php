<?php

namespace App\Modules\V1\Company\Http\Resources;

use App\Http\Resources\V1\ImageFile\ImageFileResource;
use App\Modules\V1\User\Http\Resources\UserDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => (string) $this->id,
            'companyName' => $this->company_name,
            'aboutCompany' => $this->about_company,
            'aboutCompanyJa' => $this->about_company_ja,
            'address' => $this->address,
            'status' => $this->status,
            'logo' => $this->logo
                ? url('/').'/storage/'.$this->logo
                : 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU',
            'image' => ImageFileResource::collection($this->images),
            'isEmailVerified' => $this->user->hasVerifiedEmail() ? true : false,
            'user' => $this->user
                ? new UserDetailsResource($this->user)
                : null,
            'percentage' => $this->profile_percentage,
            'jobs' => $this->user
                ? CompanyJobDetailsResource::collection($this->jobs)
                : null,
        ];
    }
}