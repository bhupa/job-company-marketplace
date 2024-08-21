<?php

namespace App\Services;

use App\Events\V1\ChatRefreshEvent;
use App\Mail\V1\SubscriptionEmail;
use App\Models\ChatRoom;
use App\Models\Payment;
use App\Models\Subscription as ModelsSubscription;
use App\Models\SubscriptionItem;
use App\Models\User;
use App\Traits\SubscriptionsTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;

class SubscriptionService extends BaseService
{
    use SubscriptionsTrait;
    protected $fireBaseService;

    public function __construct(FireBaseService $fireBaseService)
    {
        $this->fireBaseService = $fireBaseService;
    }

    public function checkWebHook($event)
    {
        // after checkout session succeeded this event is fire
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data['object'];

            $sessionId = $session->id;
            $customerId = $session->customer;
            if (
                !empty($session->metadata)
                && $session->metadata['type'] == 'chat'
            ) {
                $user = User::find($session->metadata['user_id']);
                $chatroom = ChatRoom::find($session->metadata['chatroom_id']);
                if ($user && $chatroom) {
                    $chatOutput = [
                        'type' => $session->metadata['type'],
                        'payment_intent_id' => $session->payment_intent,
                        'status' => 1,
                        'user_id' => $user->id,
                        'model_id' => $chatroom->id,
                        'model' => 'App\Models\ChatRoom',
                    ];
                    $payment = Payment::create($chatOutput);
                    $chatroom->update([
                        'payment_id' => $payment->id,
                        'payment_type' => 'stripe',
                    ]);
                    $user->device_token
                        && $this->fireBaseService->sendOtp(
                            $user,
                            'unrestricted-chat',
                            $chatroom
                        );
                    Log::info('chatroom update'.$chatroom);
                    $user
                        && broadcast(
                            new ChatRefreshEvent(['user_id' => $user->id])
                        );

                    return $payment;
                }
            } else {
                $subscriptionId = $session->subscription;
                // Retrieve subscription details from the session
                $subscription = Subscription::retrieve($subscriptionId);
                // Get the product ID associated with the subscription
                $priceId = $subscription->items->data[0]->price->id;

                $user = User::where('stripe_id', $customerId)->first();
                Log::info('checkout session complete');

                $endDate = Carbon::createFromTimestamp(
                    $subscription->current_period_end
                );

                return $subscription;
            }
        }
        // after invoice payment succeeded this event is fire
        if ($event->type === 'invoice.payment_succeeded') {
            // Retrieve subscription ID from the webhook payload
            $subscriptionId = $event->data['object']['subscription'];
            $paymentmethod = $event->data['object'];

            $method = $event->data['object']['collection_method']; // "charge_automatically"
            $subscription = Subscription::retrieve($subscriptionId);
            Log::info('invoice success for payment');
            // Get the product ID associated with the subscription
            $priceId = $subscription->items->data[0]->price->id;
            $customerId = $event->data['object']['customer'];
            $user = User::where('stripe_id', $customerId)->first();
            $endDate = Carbon::createFromTimestamp(
                $subscription->current_period_end
            );
            if ($user) {
                $user
                    ->subscriptions()
                    ->where('stripe_status', 'active')
                    ->update(['stripe_status' => 'expired']);
                Log::info('subscription details invoice');
                Log::info($subscription);
                // Create or update subscription in local database
                $localSubscription = $user->subscriptions()->updateOrCreate(
                    ['stripe_id' => $subscriptionId],
                    [
                        'stripe_price' => $priceId,
                        'name' => 'default',
                        'stripe_status' => $subscription->status,
                        'ends_at' => $endDate,
                        'quantity' => 1,
                        // Add other subscription details as needed
                    ]
                );

                $localSubscription->items()->Create(
                    [
                        'subscription_id' => $localSubscription->id,
                        'stripe_id' => $subscription['items']['data'][0]->id,
                        'stripe_product' => $subscription['items']['data'][0]->plan->product,
                        'stripe_price' => $subscription['items']['data'][0]->plan->id,
                        'quantity' => $subscription['items']['data'][0]->quantity,
                        // Add other item details as needed
                    ]
                );
                tap($user->update(['subscriptions_type' => 'stripe']));
                $product = Product::retrieve(
                    $subscription['items']['data'][0]->plan->product
                );
                $plan = getStripePlan($product);
                $data = $this->getSubscriptionForEmail(
                    $user,
                    $localSubscription,
                    $plan,
                    $paymentmethod['payment_settings']['payment_method_types']
                );
                Log::info('mail from payment intend');
                Mail::to($user->email)->send(
                    new SubscriptionEmail($user, $data)
                );
            }
        }
        // after customer subscription updated this event is fire
        if ($event->type === 'customer.subscription.updated') {
            $subscriptionId = $event->data['object']['id'];
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $subscription = Subscription::retrieve($subscriptionId);
            $paymentmethod = $event->data['object'];
            $plan = Product::retrieve(
                $subscription->items->data[0]->plan->product
            );
            $currentDate = Carbon::now();
            $ends_at = null;
            if ($plan->unit_label == 'one-week') {
                $ends_at = $currentDate->addWeeks(1);
            } elseif ($plan->unit_label == 'two-week') {
                $ends_at = $currentDate->addWeeks(2);
            } elseif ($plan->unit_label == 'one-months') {
                $ends_at = $currentDate->addMonths(1);
            } elseif ($plan->unit_label == 'three-months') {
                $ends_at = $currentDate->addMonths(3);
            } elseif ($plan->unit_label == 'six-months') {
                $ends_at = $currentDate->addMonths(6);
            }
            $customerId = $event->data['object']['customer'];
            $user = User::where('stripe_id', $customerId)->first();
            $endDate = Carbon::createFromTimestamp(
                $subscription->current_period_end
            );
            $subs = ModelsSubscription::where(
                'stripe_id',
                $subscriptionId
            )->first();
            if ($subs) {
                tap(
                    $subs->update([
                        'ends_at' => $ends_at
                            ? $ends_at->toDateTimeString()
                            : null,
                        'stripe_status' => 'active',
                    ])
                );

                if (
                    $subs->ends_at <= $currentDate
                    && $subscription->status === 'active'
                ) {
                    Log::info('auto renew plan 1 with subscription');

                    Log::info($subscription);
                    // subscription update logic here
                    $localSubscription = $user->subscriptions()->updateOrCreate(
                        ['stripe_id' => $subscriptionId],
                        [
                            'stripe_price' => $subscription['items']['data'][0]->plan->id,
                            'name' => 'default',
                            'stripe_status' => $subscription->status,
                            'ends_at' => $endDate,
                            'quantity' => 1,
                            // Add other subscription details as needed
                        ]
                    );

                    $localSubscription->items()->updateOrCreate(
                        ['stripe_id' => $subscription['items']['data'][0]->id],
                        [
                            'subscription_id' => $localSubscription->id,
                            'stripe_product' => $subscription['items']['data'][0]->plan->product,
                            'stripe_price' => $subscription['items']['data'][0]->plan->id,
                            'quantity' => $subscription['items']['data'][0]->quantity,
                            // Add other item details as needed
                        ]
                    );
                    Log::info('auto renew plan');
                    Log::info($plan);
                    Log::info('payment method');
                    // Log::info($paymentmethod['payment_method_types'][0]);
                    $plan = getStripePlan($plan);
                    $data = $this->getSubscriptionForEmail(
                        $user,
                        $localSubscription,
                        $plan,
                        'Card'
                    );
                    Mail::to($user->email)->send(
                        new SubscriptionEmail($user, $data)
                    );
                }

                return $user->update(['subscriptions_type' => 'stripe']);
            }

            return false;
        }

        // after payment_inten succeeded this event is fire
        if ($event->type === 'payment_intent.succeeded') {
            $paymentIntent = $event['data']['object'];
            if (
                $paymentIntent['status'] === 'succeeded'
                && isset($paymentIntent['metadata']['user_id'])
            ) {
                $plan = Product::retrieve(
                    $paymentIntent['metadata']['product']
                );
                $charges = $paymentIntent['charges'];

                $paymentMethodId = $charges['data'][0]['payment_method'];

                $customerId = $paymentIntent['customer'];

                if ($paymentIntent['metadata']['type'] == 'chat') {
                    $chatOutput = [
                        'type' => $paymentIntent['metadata']['type'],
                        'payment_intent_id' => $paymentIntent['id'],
                        'status' => 1,
                        'user_id' => $paymentIntent['metadata']['user_id'],
                        'model_id' => $paymentIntent['metadata']['roomId'],
                        'model' => 'App\Models\ChatRoom',
                    ];
                    $payment = Payment::create($chatOutput);
                    $chatroom = ChatRoom::find(
                        $paymentIntent['metadata']['roomId']
                    );
                    $chatroom->update(['payment_id' => $payment->id]);

                    return $payment;
                } else {
                    $currentDate = Carbon::now();
                    if ($plan->unit_label == 'one-week') {
                        $ends_at = $currentDate->addWeeks(1);
                    } elseif ($plan->unit_label == 'two-week') {
                        $ends_at = $currentDate->addWeeks(2);
                    } elseif ($plan->unit_label == 'one-months') {
                        $ends_at = $currentDate->addMonths(1);
                    } elseif ($plan->unit_label == 'three-months') {
                        $ends_at = $currentDate->addMonths(3);
                    } elseif ($plan->unit_label == 'six-months') {
                        $ends_at = $currentDate->addMonths(6);
                    }
                    Log::info('subscription details payment intented');
                    Log::info($subscription);
                    $subscription = ModelsSubscription::create([
                        'name' => $plan->name,
                        'user_id' => $paymentIntent['metadata']['user_id'],
                        'stripe_id' => $paymentIntent['id'],
                        'stripe_status' => 'active',
                        'stripe_price' => $paymentIntent['metadata']['price'],
                        'ends_at' => $ends_at, // This should be in the correct format according to your database setup
                        'quantity' => 1,
                    ]);

                    $suItem = SubscriptionItem::create([
                        'subscription_id' => $subscription->id,
                        'stripe_id' => $charges['data'][0]['id'],
                        'stripe_price' => $paymentIntent['metadata']['price'],
                        'stripe_product' => $plan->id,
                        'quantity' => 1,
                    ]);

                    return $suItem;
                }
            }
        }
    }

    public function subscriptionDetails($subscription)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $plan = Product::retrieve(
            $subscription->items->first()->stripe_product
        );
        $price = Price::retrieve($plan->default_price);
        $data['subsscribedPlan'] = [
            'id' => $plan->id,
            'name' => $plan->name,
            'description' => $plan->description,
            'features' => $plan->features,
            'default_price' => $plan->default_price,
            'amount' => $price->unit_amount / 100,
            'currency' => $price->currency,
            'endsAt' => $subscription->ends_at,
            'subscriptionId' => $subscription->id,
            'status' => $subscription->stripe_status,
        ];

        return $data;
    }

    public function cancelOld($subscriptions, $newSubscription)
    {
        ModelsSubscription::where('user_id', auth()->user()->id)
            ->where('id', '!=', $newSubscription->id)
            ->update(['stripe_status' => 'expired']);

        foreach ($subscriptions as $subscription) {
            if ($subscription->id !== $newSubscription->id) {
                $subscription->cancel();
            }
        }

        return true;
    }

    public function getSubscriptionDetails()
    {
        return $this->getSubscribedPlan();
    }
}
