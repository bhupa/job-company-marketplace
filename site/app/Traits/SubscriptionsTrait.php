<?php

namespace App\Traits;

use App\Http\Resources\V1\Esewa\EsewaResource;
use App\Http\Resources\V1\InappPurchase\InappPurchaseResourceDetails;
use App\Http\Resources\V1\Subscription\SubscriptionDetailsResource;
use Carbon\Carbon;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

trait SubscriptionsTrait
{
    public function getSubscriptionForEmail($user, $subscription, $plan, $paymentMethod)
    {
        if ($user->subscriptions_type == 'iap') {
            return [
                'customerName' => $user->fullName,
                'contractPeriod' => $plan['duration_en'][getUserLanguage($user)],
                'contractStartEnd' => $subscription->created_at->format('F j, Y').
                    ' - '.
                    $subscription->ends_at->format('F j, Y'),
                'paymentAmount' => $plan['price'][getUserLanguage($user)],
                'paymentConfirmation' => $subscription->created_at->format(
                    'F j, Y'
                ),
                'paymentMethod' => $paymentMethod,
            ];
        } else {
            return [
                'customerName' => $user->fullName,
                'contractPeriod' => $plan['timePeriod'][getUserLanguage($user)],
                'contractStartEnd' => $subscription->created_at->format('F j, Y').
                    ' - '.
                    $subscription->ends_at->format('F j, Y'),
                'paymentAmount' => $plan['price'][getUserLanguage($user)]['symbol'].
                    '.'.
                    $plan['price'][getUserLanguage($user)]['price'],
                'paymentConfirmation' => $subscription->created_at->format(
                    'F j, Y'
                ),
                'paymentMethod' => $paymentMethod,
            ];
        }
    }

    public function getSubscriptionEndDates($plan)
    {
        $currentDate = Carbon::now();
        $ends_at = null;
        if ($plan->unit_label == 'one-week' || $plan->unit_label == 'week') {
            $ends_at = $currentDate->addWeeks(1);
        } elseif ($plan->unit_label == 'two-week' || $plan->unit_label == 'two_week') {
            $ends_at = $currentDate->addWeeks(2);
        } elseif ($plan->unit_label == 'one-months' || $plan->unit_label == 'month') {
            $ends_at = $currentDate->addMonths(1);
        } elseif ($plan->unit_label == 'three-months' || $plan->unit_label == '3 months') {
            $ends_at = $currentDate->addMonths(3);
        } elseif ($plan->unit_label == 'six-months' || $plan->unit_label == '3 months') {
            $ends_at = $currentDate->addMonths(6);
        }

        return $ends_at;
    }

    public function getSubscribedPlan()
    {
        $subscription = auth()->user()->SubscribedType;
        $data = [];
        $user = auth()->user();
        Stripe::setApiKey(env('STRIPE_SECRET'));
        if (
            auth()->user()->subscriptions_type == 'iap'
            || auth()->user()->subscriptions_type == 'trial'
            || (auth()->user()->subscriptions_type == 'admin-pay'
                && auth()->user()->SubscribedType)
        ) {
            $inapp = auth()->user()->SubscribedType;
            $data = $inapp
                ? [
                    'isSubscribed' => $inapp->status == 'expired' ? false : true,
                    'plan' => getInAppPlan($inapp, $user),
                    'subscription' => new InappPurchaseResourceDetails($inapp),
                ]
                : null;
        } elseif (
            auth()->user()->subscriptions_type == 'stripe'
            && $subscription
        ) {
            $product = Product::retrieve(
                $subscription->items[0]->stripe_product
            );

            $plan = getStripePlan($product);
            $data = [
                'isSubscribed' => true,
                'plan' => $plan,
                'subscription' => new SubscriptionDetailsResource(
                    $subscription
                ),
            ];
        } elseif (
            auth()->user()->subscriptions_type == 'esewa'
            && $subscription
        ) {
            $esewa = auth()->user()->esewa();

            if ($esewa->payment_form === 'ios' || $esewa->payment_form === 'android') {
                $inapp = new \stdClass();
                $inapp->item_id = $esewa->price_id;
                $inapp->payment_type = ($esewa->payment_form == 'ios') ? 'apple' : 'google';
                $plan = getInAppPlan($inapp, $user);
                $data = [
                    'isSubscribed' => true,
                    'plan' => $plan,
                    'subscription' => new EsewaResource(
                        $esewa
                    ),
                ];
            } else {
                $price = Price::retrieve($esewa->price_id);
                // Get the product associated with the price
                $product = Product::retrieve($price->product);

                $plan = getStripePlan($product);
                $data = [
                    'isSubscribed' => true,
                    'plan' => $plan,
                    'subscription' => new EsewaResource(
                        $subscription
                    ),
                ];
            }
        }

        return $data;
    }
}
