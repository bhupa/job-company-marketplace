<?php

namespace App\Listeners\V1;

use App\Mail\V1\SubscriptionEmail;
use App\Models\V1\InAppPurchase;
use App\Traits\SubscriptionsTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Imdhemy\Purchases\Events\AppStore\Subscribed;

class AppleStoreEventListener
{
    use SubscriptionsTrait;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     */
    public function handle(Subscribed $event)
    {
        Log::info('apple store event fire');
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
        Log::info('in app  details');
        Log::info($inapp);

        try {
            // Split the JWSRenewalInfo into header, payload, and signature
            [$header, $payload, $signature] = explode(
                '.',
                $payloadArray['data']['signedTransactionInfo']
            );
            // Base64 URL-decode the header and payload
            $decodedHeader = json_decode(base64_decode($header), true);
            $decodedPayload = json_decode(base64_decode($payload), true);
            $expTimestamp = $decodedPayload['expiresDate'] / 1000;
            // Create a Carbon instance from the timestamp
            $ends_at = Carbon::createFromTimestamp($expTimestamp);
            // Format the Carbon instance as a DateTime string
            $ends_at_formatted = $ends_at->toDateTimeString();
            if ($inapp) {
                Log::info('in user details');
                Log::info($inapp->user);
                tap(
                    $inapp->update([
                        'status' => 'active',
                        'ends_at' => $ends_at_formatted,
                        'order_id' => $decodedPayload['transactionId'],
                        'price' => $decodedPayload['price'],
                        'currency' => $decodedPayload['currency'],
                    ])
                );
                Log::info('apple store event done fire');
                $inapp->user_id && subscriptionAction($inapp->user_id);
                Log::info('in after update');
                Log::info($inapp);
                $plan = getInAppPlan($inapp, $inapp->user);
                $data = $this->getSubscriptionForEmail(
                    $inapp->user,
                    $inapp,
                    $plan,
                    'Card'
                );
                Mail::to($inapp->user->email)->send(
                    new SubscriptionEmail($inapp->user, $data)
                );
            }
        } catch (\Exception $e) {
            echo 'Failed to process JWSRenewalInfo: '.$e->getMessage();
        }
    }
}
