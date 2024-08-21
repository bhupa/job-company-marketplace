<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\Subscription\PlanDetailsResource;
use Stripe\Price;
use Stripe\Product;
use Stripe\Stripe;

class ChatPriceController extends BaseController
{
    public function index()
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $products = Product::all();
        $superchat = [];
        foreach ($products as $product) {
            // Retrieve the price for this product

            if ($product->metadata['name'] === 'super_chat') {
                $price = Price::all(['product' => $product->id]);
                $product->description = 'SuperChat';
                $product->order = 1;
                // Add the price to the product object
                $product->price = $price;
                foreach ($product->price['data'] as $pdPrice) {
                    if ($pdPrice->currency == 'jpy') {
                        $product->price_jp = [
                            'id' => $pdPrice->id,
                            'price' => (string) $pdPrice->unit_amount,
                            'currency' => $pdPrice->currency,
                            'symbol' => '¥',
                        ];
                    } elseif ($pdPrice->currency == 'usd') {
                        $product->price_usd = [
                            'id' => $pdPrice->id,
                            'price' => (string) ($pdPrice->unit_amount / 100),
                            'symbol' => '$',
                            'currency' => $pdPrice->currency,
                        ];
                    } else {
                        $product->price_npr = [
                            'id' => $pdPrice->id,
                            'price' => (string) ($pdPrice->unit_amount / 100),
                            'symbol' => 'रु',
                            'currency' => $pdPrice->currency,
                        ];
                    }
                }
                $superchat[] = $product;
            }
        }

        return $this->success(
            PlanDetailsResource::collection($superchat),
            'Super Chat details'
        );
    }
}
