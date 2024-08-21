<?php

namespace App\Http\Resources\V1\Jobseeker;

use App\Http\Resources\V1\ImageFile\ImageFileResource;
use App\Http\Resources\V1\Tag\TagDetailsResource;
use App\Http\Resources\V1\User\UserDetailsResources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobseekerDetailsResource extends JsonResource
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
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'image' => ImageFileResource::collection($this->images),
            'birthday' => $this->birthday,
            'startWhen' => $this->start_when,
            'gender' => $this->gender, // male = 1 , female = 2, binary = 3
            'country' => $this->country,
            'currentCountry' => $this->current_country,
            'occupation' => (string) $this->occupation,
            'experience' => $this->experience != null ? (string) $this->experience : '0', // less than 1 year = 1, less than 2 year =2, less than 3 year = 3, 3 or more = 4
            'japaneseLevel' => $this->japanese_level != null
                    ? (string) $this->japanese_level
                    : '0', // N1 = 1 , N2 = 2, N3 = 3, N4 = 4 , N5 =5
            'about' => $this->about,
            'aboutJa' => $this->about_ja,
            'isLivingInJapan' => $this->living_japan ? true : false,
            'jobType' => (string) $this->job_type,
            'tags' => $this->tags()->count() > 0
                    ? TagDetailsResource::collection($this->tags)
                    : null,
            'percentage' => $this->profile_percentage,
            'profileImg' => $this->profile_img
                ? url('/').'/storage/'.$this->profile_img
                : ($this->gender == 2 ? url('default-avatar/female.jpg'): url('default-avatar/male.jpg')),
            'isEmailVerified' => $this->user
                ? ($this->user->hasVerifiedEmail()
                    ? true
                    : false)
                : null,
            'user' => $this->user
                ? new UserDetailsResources($this->user)
                : null,
        ];
    }
}
