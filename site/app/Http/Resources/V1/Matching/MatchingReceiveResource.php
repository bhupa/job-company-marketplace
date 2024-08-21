<?php

namespace App\Http\Resources\V1\Matching;

use App\Http\Resources\V1\Company\CompanyDetailsResource;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Http\Resources\V1\Jobseeker\JobseekerDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchingReceiveResource extends JsonResource
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
            'company' => $this->company_id
                ? new CompanyDetailsResource($this->company)
                : null,
            'job' => JobDetailsResource::collection($this->company->jobs),
            'jobseeker' => $this->job_seeker_id
                ? new JobseekerDetailsResource($this->jobseeker)
                : null,
            'createdBy' => $this->created_by
                ? new UserDetailsResources($this->createdBy)
                : null,
            'matched' => $this->matched,
            'unmatched' => $this->unmatched,
        ];
    }
}
