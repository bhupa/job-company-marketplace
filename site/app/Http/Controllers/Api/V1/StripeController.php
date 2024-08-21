<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\V1\Matching\MatchingPaymentRequest;
use App\Http\Resources\V1\Matching\MatchingResource;
use App\Services\MatchingPaymentService;
use App\Services\MatchingService;
use Illuminate\Http\Request;
use Stripe\Price;
use Stripe\Stripe;

class StripeController extends BaseController
{
    protected $matchingPayment;
    protected $matchingService;

    public function __construct(
        MatchingPaymentService $matchingPayment,
        MatchingService $matchingService
    ) {
        $this->matchingPayment = $matchingPayment;
        $this->matchingService = $matchingService;
    }

    // this function is used for
    public function paymentProcess(MatchingPaymentRequest $request)
    {
        try {
            $matching = $this->matchingService->find($request->matching_id);
            if (empty($matching->payment)) {
                $output = $this->matchingPayment->create($request);
                $message = 'Payment done successfully';
            } else {
                $output = $matching->payment['stripe_response'];
                $message = 'Already paid ';
            }
            $data['matching'] = new MatchingResource($matching);
            $data['stripeResponse'] = $matching->payment;

            return $this->success($data, $message);
        } catch (\Exception $e) {
            return $this->errors(['message' => $e->getMessage()], 400);
        }
    }

    public function paymentIntent(Request $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));
        $user = auth()->user();

        if (is_null($user->stripe_id)) {
            // If the user doesn't have a Stripe customer ID, create one
            $customer = $user->createAsStripeCustomer();
        } else {
            // If the user already has a Stripe customer ID, retrieve it
            $customer = $user->asStripeCustomer();
        }

        $chatRoomId = $request->type == 'chat' ? $request->roomId : null;
        $price = Price::retrieve($request->price);
        $amount = $price->unit_amount;
        $currency = $price->currency;
        $product = $price->product;

        $ephemeralKey = $stripe->ephemeralKeys->create(
            [
                'customer' => $customer->id,
            ],
            [
                'stripe_version' => '2022-08-01',
            ]
        );

        $paymentIntent = $stripe->paymentIntents->create([
            'amount' => $amount,
            'currency' => $currency,
            'customer' => $customer->id,
            // In the latest version of the API, specifying the `automatic_payment_methods` parameter is optional because Stripe enables its functionality by default.
            'automatic_payment_methods' => [
                'enabled' => 'true',
            ],
            'metadata' => [
                'user_id' => $user->id,
                'price' => $price,
                'product' => $product,
                'type' => $request->type,
                'roomId' => $chatRoomId,
                // Other metadata if needed
            ],
        ]);

        $data = [
            'paymentIntent' => $paymentIntent->client_secret,
            'ephemeralKey' => $ephemeralKey->secret,
            'customer' => $customer->id,
            'publishableKey' => env('STRIPE_KEY'),
        ];

        return $this->success($data, 'Pyament intent response');
    }

    public function creatTestProduct()
    {
        try {
            // Initialize Stripe client
            $stripe = new \Stripe\StripeClient(env('STRIPE_SECRET'));

            // Replace 'test_clock_subscription_id' with the actual ID of your test clock subscription
            $testClockSubscriptionId = 'sub_1P1oDYFGZt3HU4rMQyL4cuBc';

            $testClockSubscription = $stripe->subscriptions->retrieve(
                $testClockSubscriptionId
            );

            // Retrieve or create a Stripe customer for the user
            $user = auth()->user(); // Assuming you have a method to retrieve the authenticated user
            $stripeCustomer = $user->createOrGetStripeCustomer(); // Example method to retrieve or create the Stripe customer

            // Use a valid test payment method ID for card payments
            $testPaymentMethodId = 'pm_card_visa'; // Example valid test payment method (replace with appropriate test payment method ID)

            // Check if the payment method is already attached to the customer
            $attachedPaymentMethods = $stripe->paymentMethods->all([
                'customer' => $stripeCustomer->id,
                'type' => 'card',
            ]);

            $paymentMethodExists = false;

            foreach ($attachedPaymentMethods as $paymentMethod) {
                if ($paymentMethod->id === $testPaymentMethodId) {
                    $paymentMethodExists = true;
                    break;
                }
            }

            // If the payment method is not attached, attach it to the customer
            if (!$paymentMethodExists) {
                $stripe->paymentMethods->attach($testPaymentMethodId, [
                    'customer' => $stripeCustomer->id,
                ]);
            }

            // Set the attached test payment method as the default
            $stripe->customers->update($stripeCustomer->id, [
                'invoice_settings' => [
                    'default_payment_method' => $testPaymentMethodId,
                ],
            ]);

            // Subscribe the customer to the same plan as the test clock subscription
            $newSubscription = $stripe->subscriptions->create([
                'customer' => $stripeCustomer->id,
                'items' => [
                    [
                        'price' => $testClockSubscription->items->data[0]->price->id, // Assuming the test clock subscription has only one item
                    ],
                ],
            ]);

            // Optionally, handle the response or update your database
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
