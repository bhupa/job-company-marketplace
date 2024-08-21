<?php

namespace App\Listeners\V1;

use App\Models\V1\InAppPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Imdhemy\Purchases\Events\AppStore\DidRenew;

class AppStoreDidRenewListener
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
    public function handle(DidRenew $event)
    {
        Log::info('apple renew event fire');
        $subscription = $event->getServerNotification()->getSubscription();
        $uniqueIdentifier = $subscription->getUniqueIdentifier();
        $expirationTime = $subscription->getExpiryTime();
        $item = $subscription->getItemId();

        $payload = $subscription->getProviderRepresentation();
        $payloadArray = method_exists($payload, 'toArray')
            ? $payload->toArray()
            : (array) $payload;

        $inapp = InAppPurchase::where('item_id', $item)
            ->where('store_user_id', $uniqueIdentifier)
            ->latest()
            ->first();

        try {
            // Split the JWSRenewalInfo into header, payload, and signature
            [$header, $payload, $signature] = explode(
                '.',
                $payloadArray['data']['signedTransactionInfo']
            );
            // Base64 URL-decode the header and payload
            $decodedHeader = json_decode(base64_decode($header), true);
            $decodedPayload = json_decode(base64_decode($payload), true);
            // Convert milliseconds to seconds

            $expTimestamp = $decodedPayload['expiresDate'] / 1000;
            // Create a Carbon instance from the timestamp
            $ends_at = Carbon::createFromTimestamp($expTimestamp);
            // Format the Carbon instance as a DateTime string
            $ends_at_formatted = $ends_at->toDateTimeString();

            $input = [
                'store_user_id' => $uniqueIdentifier,
                'paymentFor' => 'subscription',
                'ends_at' => $ends_at_formatted,
                'item_id' => $item,
                'payment_type' => 'apple',
                'payment_for' => 'subscription',
                'status' => 'active',
                'order_id' => $decodedPayload['transactionId'],
                'price' => $decodedPayload['price'],
                'currency' => $decodedPayload['currency'],
                'user_id' => $inapp->user_id,
            ];
            $output = InAppPurchase::create($input);
            Log::info('apple renew done event fire');
            $output->user_id && subscriptionAction($output->user_id);
            InAppPurchase::whereNot('id', $output->id)
                ->where('user_id', $output->user_id)
                ->where('store_user_id', $uniqueIdentifier)
                ->update(['status' => 'expired']);
        } catch (\Exception $e) {
            echo 'Failed to process JWSRenewalInfo: '.$e->getMessage();
        }
    }
}
