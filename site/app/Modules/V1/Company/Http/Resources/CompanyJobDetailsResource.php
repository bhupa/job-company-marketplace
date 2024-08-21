<?php

namespace App\Modules\V1\Company\Http\Resources;

use App\Http\Resources\V1\Job\JobCategoryResource;
use App\Http\Resources\V1\JobLocation\DistrictDetailsResource;
use App\Http\Resources\V1\Tag\TagDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyJobDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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
            'requiredSkills' => $this->required_skills,
            'requiredSkillsJa' => $this->required_skills_ja,
            'fromWhen' => $this->from_when,
            'tags' => $this->tags
                ? TagDetailsResource::collection($this->tags)
                : null,
        ];
    }
}