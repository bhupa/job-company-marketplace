<?php

namespace App\Console\Commands;

use App\Jobs\SubscriptionExpiredNotification;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class CheckSubscrutpionPlanExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-subscrutpion-plan-expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check subscription plan expire every day at mid-night';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $today = now();
        $subscriptions = Subscription::where('stripe_status', 'active')
            ->with('user')
            ->get();

        $chechToday = $today->startOfDay();
        foreach ($subscriptions as $subscription) {
            $endsAt = Carbon::parse($subscription->ends_at);
            $price = Price::retrieve($subscription->stripe_price);
            $plan = Product::retrieve($price->product);
            $user = $subscription->user;
            $expire = $endsAt->startOfDay();
            if (
                $plan->unit_label == 'one-week' ||
                $plan->unit_label == 'two-week'
            ) {
                // Calculate the difference in hours between $createdAt and $today
                $hoursDifference = $endsAt->diffInHours($today);
                // Check if the difference is less than 24 hours (1 day)
                if ($hoursDifference < 24) {
                    // Send an email
                    SubscriptionExpiredNotification::dispatch(
                        $subscription,
                        $plan,
                        $price,
                        $user
                    );
                }
            } elseif (
                $plan->unit_label == 'one-months' ||
                $plan->unit_label == 'three-months' ||
                $plan->unit_label == 'six-months'
            ) {
                // Calculate the difference in days between $createdAt and $today
                $daysDifference = $endsAt->diffInDays($today);

                // Check if the difference is less than or equal to 7 days (1 week)
                if ($daysDifference <= 7) {
                    // Send an email
                    SubscriptionExpiredNotification::dispatch(
                        $subscription,
                        $plan,
                        $price,
                        $user
                    );
                }
            }

            if ($expire->eq($chechToday)) {
                $subscription->update(['stripe_status' => 'expired']);
            }
        }

        return true;
    }
}
