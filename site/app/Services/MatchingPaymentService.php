<?php

namespace App\Services;

use App\Models\MatchingPayment;
use Stripe\Charge;
use Stripe\Stripe;

class MatchingPaymentService extends BaseService
{
    public function __construct(MatchingPayment $matchingPayment)
    {
        $this->model = $matchingPayment;
    }

    public function create($request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $token = $request->stripeToken;
        $charge = Charge::create([
            'amount' => 1000,
            'currency' => 'usd',
            'description' => 'Example test',
            'source' => $token,
        ]);

        return $this->model->create([
            'matching_id' => $request->matching_id,
            'stripe_response' => $charge->jsonSerialize(),
        ]);
    }
}
