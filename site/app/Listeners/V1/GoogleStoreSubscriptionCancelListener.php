<?php

namespace App\Listeners\V1;

use App\Models\V1\InAppPurchase;
use Illuminate\Support\Facades\Log;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionCanceled;

class GoogleStoreSubscriptionCancelListener
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
    public function handle(SubscriptionCanceled $event)
    {
        Log::info('google cancel event fire');
        // The following data can be retrieved from the event
        $subscription = $event->getServerNotification()->getSubscription();
        $uniqueIdentifier = $subscription->getUniqueIdentifier();
        $expirationTime = $subscription->getExpiryTime();
        $item = $subscription->getItemId();

        $inapp = InAppPurchase::where('item_id', $item)
            ->where('store_user_id', $uniqueIdentifier)
            ->latest()
            ->first();

        try {
            tap($inapp->update(['status' => 'cancel']));
            Log::info('google cancel done event fire');
            $inapp->user_id && subscriptionAction($inapp->user_id);
        } catch (\Exception $e) {
            echo 'Failed to process JWSRenewalInfo: '.$e->getMessage();
        }
    }
}
