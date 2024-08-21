<?php

namespace Tests\Feature\V1\Api;

use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SocketsTest extends TestCase
{
    protected $baseUrl;

    /**
     * A basic feature test example.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = env('APP_URL'); // Or set it to any dynamic URL as needed
    }

    public function testSocketConnection(): void
    {
        Artisan::call('websocket:serve');

        // Your Laravel application logic that triggers WebSocket events
        // For example, dispatch an event or make a WebSocket connection in your code

        // Wait for some time to let the WebSocket server process events
        sleep(2);
    }
}
