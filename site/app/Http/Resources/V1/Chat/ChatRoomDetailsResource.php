<?php

namespace App\Http\Resources\V1\Chat;

use App\Http\Resources\V1\Matching\MatchingResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatRoomDetailsResource extends JsonResource
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
            'name' => $this->name,
            'type' => $this->type,
            'isAccepted' => $this->match
                ? ($this->match->matched
                    ? true
                    : false)
                : false,
            'image' => $this->image
                ? url('/').'/storage/'.$this->image
                : null,
            'status' => $this->status,
            'unseen' => $this->unseen_count,
            'lastSeenId' => $this->lastSeenId
                ? (string) $this->lastSeenId
                : null,
            'adminAssit' => $this->admin_assist ? true : false,
            'isDeleted' => $this->created_by ? false : true,
            'superChat' => $this->super_chat ? true : false,
            'isChatViolation' => $this->violation
                ? ($this->violation->status
                    ? true
                    : false)
                : false,
            'payment_id' => (string) $this->payment_id,
            'matchedUser' => $this->getMatchedUserAttribute(auth()->user()),
            'createdBy' => [
                'userId' => (string) $this->created_by,
                'userType' => $this->createdBy
                    ? $this->createdBy->user_type
                    : null,
                'id' => $this->created_by
                    ? ($this->createdBy->user_type == 1
                        ? (string) $this->createdBy->jobseeker->id
                        : (string) $this->createdBy->company->id)
                    : null,
                'name' => $this->created_by
                    ? ($this->createdBy->user_type == 1
                        ? $this->createdBy->jobseeker->first_name.
                            ' '.
                            $this->createdBy->jobseeker->last_name
                        : $this->createdBy->company->company_name)
                    : null,
                'image' => $this->created_by
                    ? ($this->createdBy->user_type == 1
                        ? url('/').
                            '/storage/'.
                            $this->createdBy->jobseeker->profile_img
                        : url('/').
                            '/storage/'.
                            $this->createdBy->company->logo)
                    : null,
            ],
            'user' => [
                'userId' => $this->user_id ? (string) $this->user_id : null,
                'userType' => $this->user_id ? $this->user->user_type : null,
                'id' => $this->user_id
                    ? ($this->user->user_type == 1
                        ? (string) $this->user->jobseeker->id
                        : (string) $this->user->company->id)
                    : null,
                'name' => $this->user_id
                    ? ($this->user->user_type == 1
                        ? $this->user->jobseeker->first_name.
                            ' '.
                            $this->user->jobseeker->last_name
                        : $this->user->company->company_name)
                    : null,
                'image' => $this->user_id
                    ? ($this->user->user_type == 1
                        ? url('/').
                            '/storage/'.
                            $this->user->jobseeker->profile_img
                        : url('/').'/storage/'.$this->user->company->logo)
                    : null,
            ],
            'match' => $this->matching_id
                ? new MatchingResource($this->match)
                : null,
            'chats' => new ChatDetailsResource(
                $this->chats()->latest()->take(1)->first()
            ),
        ];
    }
}
