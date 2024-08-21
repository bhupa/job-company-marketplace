<?php

namespace App\Http\Resources\V1\User;

use App\Http\Resources\V1\ImageFile\ImageFileResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResources extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = [1 => 'active', 2 => 'deactive', 3 => 'restricted'];

        return [
            'id' => (string) $this->id,
            'email' => $this->email,
            'countryCode' => $this->country_code,
            'phone' => $this->phone,
            'introVideo' => $this->intro_video
                ? url('/').'/storage/'.$this->intro_video
                : null,
            'language' => getUserLanguage($this),

            'subscriptionType' => $this->subscriptions_type,
            'isSubscribed' => $this->subscribed_type
                ? ($this->subscribed_type->status == 'expired'
                    ? false
                    : true)
                : false,
            'subscriptionStatus' => $this->subscribed_type
                ? ($this->subscriptions_type == 'stripe'
                    ? $this->subscribed_type->stripe_status
                    : $this->subscribed_type->status)
                : '',
            'userType' => $this->user_type,
            'status' => $this->status,
            'isProfileComplete' => $this->user_type == 1
                    ? ($this->jobseeker
                        ? true
                        : false)
                    : ($this->company
                        ? true
                        : false),
            'deviceToken' => $this->device_token ? $this->device_token : null,
            'googleId' => $this->google_id,
            'facebookId' => $this->facebook_id,
            'appleId' => $this->apple_id,
            'isPasswordSet' => $this->password ? true : false,
            'isEmailVerified' => $this->hasVerifiedEmail() ? true : false,
            'isViolation' => $this->violation && $this->violation->status ? true : false,
            'verificationStatus' => $this->getVetificationAttribute(),
            'verification' => [
                'comment' => $this->comment ? $this->comment : '',
                'documents' => $this->documents
                    ? ImageFileResource::collection($this->documents)
                    : null,
            ],
        ];
    }
}
