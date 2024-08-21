<?php

namespace App\Http\Resources\V1\Matching;

use App\Http\Resources\V1\Company\CompanyDetailsResource;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Http\Resources\V1\Jobseeker\JobseekerDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchingSentResource extends JsonResource
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
            'jobseeker' => $this->job_seeker_id
                ? new JobseekerDetailsResource($this->jobseeker)
                : null,
            'job' => $this->job_id ? new JobDetailsResource($this->job) : null,
            'createdBy' => $this->created_by
                ? new UserDetailsResources($this->createdBy)
                : null,
            'matched' => $this->matched,
            'unmatched' => $this->unmatched,
            // 'dailyCount' => (count(auth()->user()->flips->where('created_at', Carbon::now())->get()) + 1),
        ];
    }
}
