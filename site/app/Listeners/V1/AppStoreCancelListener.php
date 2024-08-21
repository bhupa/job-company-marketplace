<?php

namespace App\Listeners\V1;

use App\Models\V1\InAppPurchase;
use Illuminate\Support\Facades\Log;
use Imdhemy\Purchases\Events\AppStore\DidChangeRenewalStatus;

class AppStoreCancelListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(DidChangeRenewalStatus $event)
    {
        // Log::info($event);
        // The following data can be retrieved from the event
        Log::info('apple cancel event fire');
        $subscription = $event->getServerNotification()->getSubscription();
        $uniqueIdentifier = $subscription->getUniqueIdentifier();
        $expirationTime = $subscription->getExpiryTime();
        $item = $subscription->getItemId();

        $payload = $subscription->getProviderRepresentation();

        $inapp = InAppPurchase::where('item_id', $item)
            ->where('store_user_id', $uniqueIdentifier)
            ->latest()
            ->first();
        if ($inapp) {
            tap($inapp->update(['status' => 'cancel']));
            Log::info('apple cancel done event fire');
            $inapp->user_id && subscriptionAction($inapp->user_id);
        }
    }
}
