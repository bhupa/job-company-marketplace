<?php

namespace App\Http\Resources\V1\Company;

use App\Http\Resources\V1\ImageFile\ImageFileResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyDetailsResource extends JsonResource
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
            'companyName' => $this->company_name,
            'aboutCompany' => $this->about_company,
            'aboutCompanyJa' => $this->about_company_ja,
            'address' => $this->address,
            'status' => $this->status,
            'logo' => $this->logo
                ? url('/').'/storage/'.$this->logo
                : url('default-avatar/company.jpg'),
            'image' => ImageFileResource::collection($this->images),
            'isEmailVerified' => $this->user->hasVerifiedEmail() ? true : false,
            'user' => $this->user
                ? new UserDetailsResources($this->user)
                : url('default-avatar/company.jpg'),
            'percentage' => $this->profile_percentage,
            'jobs' => $this->user
                ? CompanyJobDetailsResource::collection($this->jobs)
                : null,
        ];
    }
}
