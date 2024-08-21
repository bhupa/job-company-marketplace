<?php

namespace App\Http\Resources\V1\InappPurchase;

use App\Http\Resources\V1\User\UserDetailsResources;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InappPurchaseResourceDetails extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $today = Carbon::now();
        $enddate = $this->ends_at
            ? Carbon::createFromDate($this->ends_at)
            : null;

        return [
            'plan' => $this->plan,
            'user' => new UserDetailsResources($this->user),
            'itemId' => $this->item_id,
            'subscriptionStatus' => $this->status,
            'purchaseToken' => $this->purchase_token,
            'paymentType' => $this->payment_type,
            'paymentFor' => $this->payment_for,
            'transactionReceipt' => $this->transaction_receipt, // 'google' | 'apple'
            'trialEndsAt' => $this->trial_ends_at,
            'endsAt' => $this->ends_at,
            'remainingDays' => $enddate ? $today->diffInDays($enddate) : null,
            'orderId' => $this->orderId,
            'price' => $this->price,
            'currency' => $this->currency,
            'storeUserId' => $this->store_user_id,
            'subscriptionType' => $this->user
                ? $this->user->subscriptions_type
                : null,
        ];
    }
}
