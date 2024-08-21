<?php

namespace App\Modules\V1\Jobseeker\Http\Resources;

use App\Http\Resources\V1\ImageFile\ImageFileResource;
use App\Http\Resources\V1\TagDetailsResource;
use App\Modules\V1\User\Http\Resources\UserDetailsResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobseekerDetailsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
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
                : 'https://fastly.picsum.photos/id/0/5000/3333.jpg?hmac=_j6ghY5fCfSD6tvtcV74zXivkJSPIfR9B8w34XeQmvU',
            'isEmailVerified' => $this->user
                ? ($this->user->hasVerifiedEmail()
                    ? true
                    : false)
                : null,
            'user' => $this->user
                ? new UserDetailsResource($this->user)
                : null,
        ];
    }
}