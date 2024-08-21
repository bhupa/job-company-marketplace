<?php

namespace App\Listeners\V1;

use App\Mail\V1\SubscriptionEmail;
use App\Models\V1\InAppPurchase;
use App\Traits\SubscriptionsTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Imdhemy\Purchases\Events\GooglePlay\SubscriptionPurchased;

class GoogleStoreEventListener
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
    public function handle(SubscriptionPurchased $event)
    {
        Log::info('google store event fire');
        try {
            // Retrieve data from the event
            $subscription = $event->getServerNotification()->getSubscription();
            $uniqueIdentifier = $subscription->getUniqueIdentifier();
            $expirationTime = $subscription->getExpiryTime();
            $item = $subscription->getItemId();

            // Get provider representation and convert to array
            $subDetails = $subscription->getProviderRepresentation();
            $payloadArray = method_exists($subDetails, 'toArray')
                ? $subDetails->toArray()
                : (array) $subDetails;

            // Check if in-app purchase already exists
            $inapp = InAppPurchase::where('item_id', $item)
                ->where('store_user_id', $uniqueIdentifier)
                ->latest()
                ->first();
            $priceDollars = $payloadArray['priceAmountMicros'] / 1000000;
            $formattedPrice = number_format($priceDollars, 2);

            $expTimestamp = $payloadArray['expiryTimeMillis'] / 1000;
            $ends_at = Carbon::createFromTimestamp($expTimestamp);
            $ends_at_formatted = $ends_at->toDateTimeString();

            // If in-app purchase doesn't exist, create a new one
            if (empty($inapp)) {
                $input = [
                    'store_user_id' => $uniqueIdentifier,
                    'ends_at' => $ends_at_formatted,
                    'item_id' => $item,
                    'payment_type' => 'google',
                    'payment_for' => 'subscription',
                    'status' => 'active',
                    'order_id' => $payloadArray['orderId'],
                    'price' => $formattedPrice,
                    'currency' => $payloadArray['priceCurrencyCode'],
                    'user_id' => $inapp->user_id ?? null, // Check if $inapp exists before accessing user_id
                ];
                $output = InAppPurchase::create($input);
                Log::info('google store event fire');
                $output->user_id && subscriptionAction($output->user_id);
                $plan = getInAppPlan($output, $output->user);
                $data = $this->getSubscriptionForEmail(
                    $output->user,
                    $output,
                    $plan,
                    'Card'
                );
                Mail::to($output->user->email)->send(
                    new SubscriptionEmail($output->user, $data)
                );
            } else {
                $input = [
                    'status' => 'active',
                    'ends_at' => $ends_at_formatted,
                    'order_id' => $payloadArray['orderId'],
                    'price' => $formattedPrice,
                    'currency' => $payloadArray['priceCurrencyCode'],
                ];
                tap($inapp->update($input));
                $output = $inapp;
                $output->user_id && subscriptionAction($output->user_id);
                $plan = getInAppPlan($inapp, $output->user);
                $data = $this->getSubscriptionForEmail(
                    $output->user,
                    $output,
                    $plan,
                    'Card'
                );
                Mail::to($output->user->email)->send(
                    new SubscriptionEmail($output->user, $data)
                );
            }
        } catch (\Exception $e) {
            // Log any exceptions that occur during processing
            Log::error(
                'Failed to process Google Play Store subscription purchase: '.
                    $e->getMessage()
            );
        }
    }
}
