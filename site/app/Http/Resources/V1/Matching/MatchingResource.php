<?php

namespace App\Http\Resources\V1\Matching;

use App\Http\Resources\V1\Company\CompanyDetailsResource;
use App\Http\Resources\V1\Job\JobDetailsResource;
use App\Http\Resources\V1\Jobseeker\JobseekerDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MatchingResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // $count = auth()->user()->flips->where('created_at', '>=', Carbon::now()->startOfDay())->count();

        return [
            'id' => (string) $this->id,
            'company' => $this->company_id
                ? new CompanyDetailsResource($this->company)
                : null,
            'jobseeker' => $this->job_seeker_id
                ? new JobseekerDetailsResource($this->jobseeker)
                : null,
            'job' => $this->job_id ? new JobDetailsResource($this->job) : null,
            //  'favourite'=> $this->favourite_by ? new UserDetailsResources($this->favourite) : null,
            'createdBy' => $this->created_by
                ? new UserDetailsResources($this->createdBy)
                : null,
            'matched' => $this->matched,
            'unmatched' => $this->unmatched,
            'isPaid' => $this->payment ? true : false,
            'requestFavouriteId' => (string) $this->parent_id,
            'room' => $this->room ? (string) $this->room->id : null,
            //  'dailyCount' => $count ? ($count + 1) : null,
        ];
    }
}
