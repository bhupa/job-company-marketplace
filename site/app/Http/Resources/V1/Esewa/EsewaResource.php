<?php

namespace App\Http\Resources\V1\Esewa;

use App\Http\Resources\V1\User\UserDetailsResources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EsewaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $enddate = Carbon::createFromDate($this->ends_at);
        $endsAt = Carbon::parse($this->ends_at)->setTimezone('UTC');

        $today = Carbon::now()->startOfDay();

        return [
            'stripeId' => $this->id,
            'subscriptionStatus' => $this->status,
            'subscriptionType' => $this->user->subscriptions_type,
            'endsAt' => $this->ends_at,
            'remainingDays' => $today->diffInDays($enddate),
            'paymentFrom' => $this->payment_form,
            'user' => new UserDetailsResources($this->user),
        ];
    }
}
