<?php

namespace App\Listeners\V1;

use App\Models\V1\InAppPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionRenewed;

class GoogleStoreSubscriptionRenew
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
    public function handle(SubscriptionRenewed $event)
    {
        Log::info('google renew event fire');
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
            $subDetails = $subscription->getProviderRepresentation();
            $payloadArray = method_exists($subDetails, 'toArray')
                ? $subDetails->toArray()
                : (array) $subDetails;

            $priceDollars = $payloadArray['priceAmountMicros'] / 1000000; // Divide by 1,000,000 to convert micros to dollars
            $formattedPrice = number_format($priceDollars, 2);

            $expTimestamp = $payloadArray['expiryTimeMillis'] / 1000;
            // Create a Carbon instance from the timestamp
            $ends_at = Carbon::createFromTimestamp($expTimestamp);
            // Format the Carbon instance as a DateTime string
            $ends_at_formatted = $ends_at->toDateTimeString();
            $input = [
                'store_user_id' => $uniqueIdentifier,
                'ends_at' => $ends_at_formatted,
                'item_id' => $item,
                'payment_type' => 'google',
                'payment_for' => 'subscription',
                'purchase_token' => $inapp->purchase_token,
                'transaction_receipt' => $inapp->transaction_receipt,
                'status' => 'active',
                'order_id' => $payloadArray['orderId'],
                'price' => $formattedPrice,
                'currency' => $payloadArray['priceCurrencyCode'],
                'user_id' => $inapp->user_id,
            ];
            $output = InAppPurchase::create($input);
            $output->user_id && subscriptionAction($output->user_id);
            Log::info('google renew done event fire');
            InAppPurchase::whereNot('id', $output->id)
                ->where('user_id', $output->user_id)
                ->where('store_user_id', $uniqueIdentifier)
                ->update(['status' => 'expired']);
        } catch (\Exception $e) {
            echo 'Failed to process JWSRenewalInfo: '.$e->getMessage();
        }
    }
}
