<?php

namespace Tests\Feature\V1\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class UserRegistrationTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    // use RefreshDatabase; // Refresh the database before each test
    use WithFaker; // Faker to generate fake data

    protected $baseUrl;

    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = env('APP_URL'); // Or set it to any dynamic URL as needed
    }

    public function testCanRegisterAUser(): void
    {
        $userData = [
            // 'name' => $this->faker->name,
            'email' => $this->faker->safeEmail,
            'country_code' => $this->faker->randomElement([
                '+1',
                '+977',
                '+44',
                '+61',
            ]),
            'phone' => $this->faker->phoneNumber,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'user_type' => 1,
        ];

        $response = $this->postJson(
            $this->baseUrl . 'api/v1/register',
            $userData
        );

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                'user' => [
                    'id',
                    'email',
                    'userType', // Update field name from 'user_type' to 'userType'
                    'phone',
                    'countryCode', // Update field name from 'country_code' to 'countryCode'
                    // Add more user fields to validate
                ],
                'token',
            ],
        ]);

        // Additional assertions if your API returns specific messages or status
        $response->assertJson([
            'success' => true,
            'message' => 'User register sucessfully',
        ]);
    }
}
