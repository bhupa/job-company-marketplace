<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\Esewa\EsewaResource;
use App\Http\Resources\V1\InappPurchase\InappPurchaseResourceDetails;
use App\Http\Resources\V1\Subscription\PlanDetailsResource;
use App\Http\Resources\V1\Subscription\SubscriptionDetailsResource;
use App\Models\Plan;
use App\Models\Subscription as ModelsSubscription;
use App\Models\V1\InAppPurchase;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Google\Service\Logging\Resource\Logs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;
use Stripe\Subscription;
use Stripe\Webhook;
use Imdhemy\AppStore\WebhookNotification;
use Imdhemy\AppStore\Exceptions\InvalidWebhookNotificationException;
use Imdhemy\AppStore\Exceptions\InvalidSharedSecretException;

use Imdhemy\GooglePlay\WebhookNotification as GooglePlayWebhookNotification;
use Imdhemy\GooglePlay\Exceptions\InvalidNotificationException;
use Illuminate\Support\Str;
use Stripe\Checkout\Session;

class SubscriptionController extends BaseController
    {
    protected $subscriptionService;

    public function __construct(SubscriptionService $subscriptionService)
        {
        $this->subscriptionService = $subscriptionService;
        }

    public function index()
        {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $products = Product::all();

        $subscriptions = [];
        $data = [];
        foreach ($products as $product) {
            // dd($product->metadata['name_ja']);

            if (
                Str::contains($product->metadata['name'], 'Jobseeker') &&
                auth()->user()->user_type == 1
            ) {
                $data = getStripePlan($product);
                $subscriptions[] = $data;
                } elseif (
                Str::contains($product->metadata['name'], 'Company') &&
                auth()->user()->user_type == 2
            ) {
                $data = getStripePlan($product);
                $subscriptions[] = $data;
                }
            }
        usort($subscriptions, function ($a, $b) {
            return $a['order'] <=> $b['order'];
            });
        return $this->success($subscriptions, 'Plan Lists');

        // return response()->json(['message' => 'Plan Lists', 'data' => SubscriptionDetailsResource::collection($products)], 200);
        }

    public function webhook(Request $request)
        {
        Log::info('web hook fire');
        // dd('test');
        $webHookSecret = env('STRIPE_WEBHOOK_SECRET');
        $plyload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        Stripe::setApiKey(env('STRIPE_SECRET'));
        try {
            $event = Webhook::constructEvent(
                $plyload,
                $sigHeader,
                $webHookSecret
            );
            Log::info('stripe every this is fine');
            $this->subscriptionService->checkWebHook($event);
            Log::info('webhook return successfully');
            return response()->json(['message' => 'Webhook received'], 200);
            } catch (SignatureVerificationException $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
            }
        }

    public function addPlan(Request $request)
        {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $token = $request->stripeToken;
        $user = auth()->user();
        try {
            if (is_null($user->stripe_id)) {
                // If the user doesn't have a Stripe customer ID, create one
                $stripeCustomer = $user->createAsStripeCustomer();
                } else {
                // If the user already has a Stripe customer ID, retrieve it
                $stripeCustomer = $user->asStripeCustomer();
                }

            $prices = Price::all([
                'lookup_keys' => [$request->lookup_key],
                'expand' => ['data.product'],
            ]);

            $session = Session::create([
                'customer' => $stripeCustomer->id,
                'line_items' => [
                    [
                        'price' => $prices->data[0]->id,
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'subscription',
                'success_url' => env('APP_URL') . 'profile?success=true',
                'cancel_url' => env('APP_URL') . 'profile?canceled=true',
            ]);

            // Return a success response
            return response()->json([
                'sessionId' => $session->id,
                'session_url' => $session->url,
            ]);
            // $result = $user->newSubscription('default', $request->stripe_plan)->create('pm_card_visa');

            // return $this->success($result, 200);
            } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
            }
        }

    public function pausePlan(Request $request)
        {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $user = auth()->user();

            if ($user->subscribed('default')) {
                $subs = $user->subscriptions()
                    ->latest('created_at')
                    ->first();
                $subscription = Subscription::retrieve($subs->stripe_id);
                $subscription->cancel();
                $user->subscriptions()
                    ->latest('created_at')
                    ->update(['stripe_status' => 'cancel']);

                }
            return $this->success([], 'Subscription paused successfully');
            } catch (Exception $e) {
            // Handle any errors that occur during the subscription update

            return $this->errors(
                [
                    'message' =>
                        'Error pausing subscription: ' . $e->getMessage(),
                ],
                400
            );
            }

        }

    public function destroy($id)
        {
        try {
            Stripe::setApiKey(env('STRIPE_SECRET'));
            $subscription = ModelsSubscription::with('item')->find($id);
            // $sus = Subscription::retrieve($subscription->stripe_id);
            // $sus->cancel();
            $subscription->item->delete();
            $subscription->delete();

            return $this->success([], 'Subscription plan deleted successfully');
            } catch (Exception $e) {
            // Handle any errors that occur during plan deletion

            return $this->errors(
                [
                    'message' =>
                        'Error deleting subscription plan: ' . $e->getMessage(),
                ],
                400
            );
            }
        }

    public function getSubscribPlan()
        {
        $result = $this->subscriptionService->getSubscriptionDetails();

        if ($result) {
            return $this->success($result, 'Subscribed user plan');
            }
        return $this->success(
            ['isSubscribed' => false, 'plan' => null, 'subscription' => null],
            'Not any plan is subscribed yet'
        );
        
        }
    }