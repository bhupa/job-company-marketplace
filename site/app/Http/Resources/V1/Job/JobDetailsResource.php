<?php

namespace App\Http\Resources\V1\Job;

use App\Http\Resources\V1\Company\CompanyDetailsResource;
use App\Http\Resources\V1\JobLocation\DistrictDetailsResource;
use App\Http\Resources\V1\Tag\TagDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobDetailsResource extends JsonResource
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
            'jobTitle' => $this->job_title,
            'jobTitleJa' => $this->job_title_ja,
            'occupation' => new JobCategoryResource($this->occupations),
            'jobLocation' => $this->locations()->count() > 0
                    ? DistrictDetailsResource::collection($this->locations)
                    : null,
            'jobImage' => $this->image
                ? url('/').'/storage/'.$this->image->image
                : null,
            'salaryFrom' => $this->salary_from,
            'salaryTo' => $this->salary_to,
            'gender' => (string) $this->gender,
            'experience' => $this->experience != null ? (string) $this->experience : '0',
            'japaneseLevel' => $this->japanese_level != null
                    ? (string) $this->japanese_level
                    : '0',
            'published' => $this->published,
            'requiredSkills' => $this->required_skills,
            'requiredSkillsJa' => $this->required_skills_ja,
            'fromWhen' => $this->from_when,
            'status' => $this->status ? 1 : 0,
            'jobType' => (string) $this->job_type,
            'payType' => (string) $this->pay_type,
            'tags' => $this->tags
                ? TagDetailsResource::collection($this->tags)
                : null,
            'user' => new UserDetailsResources($this->user),
            'company' => $this->user
                ? ($this->user->company
                    ? new CompanyDetailsResource($this->user->company)
                    : null)
                : null,
        ];
    }
}
