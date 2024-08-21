<?php

namespace Tests\Feature\V1\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Imdhemy\GooglePlay\ClientFactory;
use Imdhemy\Purchases\Facades\Subscription;

class GoogleStoreTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $responseBody = [
            'kind' => 'androidpublisher#subscriptionPurchase',
            'startTimeMillis' => '1580000000000',
            'expiryTimeMillis' => '1580000000000',
            'autoRenewing' => true,
            'priceCurrencyCode' => 'USD',
            'priceAmountMicros' => '1000000',
            'countryCode' => 'US',
            // Other fields are omitted for brevity
        ];

        $response = new Response(200, [], json_encode($responseBody));
        $client = ClientFactory::mock($response);
        //    Log::info('response'.json_encode($responseBody));
        // use the client to verify the purchase
        // The returned attributes are the same as the mocked response

        // THE FOLLOWING LINE SHOULD BE IN YOUR CLIENT CODE
        $receipt = Subscription::googlePlay($client)
            ->id('company_one_week_subscription')
            ->token(
                'ocljbkecjllimhaicidlagka.AO-J1Oz9HOshfdb9KDWtHevrZKtZyYy1zm0YQI0WX-RXE9JoQsgJlSlK2dx_eX5_Rl1CRUjQ6cpNnqwFSvgY8DY4Uni0WpX65Q'
            )
            ->get();
        dd($receipt);
    }
}
