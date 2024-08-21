<?php

namespace App\Http\Resources\V1\Matching;

use App\Http\Resources\V1\Company\CompanyDetailsResource;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Http\Resources\V1\Jobseeker\JobseekerDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavouriteResource extends JsonResource
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
            'company' => new CompanyDetailsResource($this->company),
            'jobseeker' => new JobseekerDetailsResource($this->jobseeker),
            'job' => $this->job_id && $this->job
                    ? JobDetailsResource::collection([$this->job])
                    : null,
            'isRequestSent' => $this->child ? true : false,
            // 'user' => new UserDetailsResources($user),
        ];
    }
}
