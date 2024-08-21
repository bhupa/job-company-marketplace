<?php

namespace App\Services;

use App\Models\Payment;
use Stripe\Charge;
use Stripe\Stripe;

class PaymentService extends BaseService
{
    public function __construct(Payment $payment)
    {
        $this->model = $payment;
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
