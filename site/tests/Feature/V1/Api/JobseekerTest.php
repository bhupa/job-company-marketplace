<?php

namespace Tests\Feature\V1\Api;

use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class JobseekerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use WithFaker;
    protected $baseUrl;

    public function setUp(): void
    {
        parent::setUp();
        $this->baseUrl = env('APP_URL'); // Or set it to any dynamic URL as needed
    }

    public function testLoginAndCreateJobseekerProfile(): void
    {
        // Create a user for login
        $user = User::factory()->create([
            'email' => $this->faker->safeEmail,
            'country_code' => $this->faker->randomElement([
                '+1',
                '+977',
                '+44',
                '+61',
            ]),
            'phone' => $this->faker->phoneNumber,
            'password' => 'password123',
            'user_type' => 1,
        ]);

        // Login endpoint API request
        $loginResponse = $this->postJson($this->baseUrl . 'api/v1/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $loginResponse->assertStatus(200); // Assuming successful login
        // Generate authentication token
        $token = $loginResponse['data']['token']; // Replace 'token' with the actual token key in your response

        // Profile creation endpoint API request
        $profileResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->postJson($this->baseUrl . 'api/v1/jobseeker/store', [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            // 'image'=>null,
            'birthday' => now()
                ->subYears(rand(18, 60))
                ->subMonths(rand(0, 11))
                ->subDays(rand(0, 30)),
            'gender' => rand(1, 3),
            'experience' => rand(1, 4),
            'occupation' => rand(1, 9),
            'japanese_level' => rand(1, 5),
            'country' => $this->faker->country,
            'current_country' => $this->faker->countryCode,
            'about' => $this->faker->text(300),
        ]);

        $profileResponse->assertStatus(200);
        $jobseekerId = $loginResponse['data']['id']; // Replace 'token' with the actual token key in your response
        $profileResponse = $this->withHeaders([
            'Authorization' => 'Bearer ' . $token,
        ])->getJson($this->baseUrl . 'api/v1/jobseeker/store', [
            'first_name' => $this->faker->firstName,
            'last_name' => $this->faker->lastName,
            // 'image'=>null,
            'birthday' => now()
                ->subYears(rand(18, 60))
                ->subMonths(rand(0, 11))
                ->subDays(rand(0, 30)),
            'gender' => rand(1, 3),
            'experience' => rand(1, 4),
            'occupation' => rand(1, 9),
            'japanese_level' => rand(1, 5),
            'country' => $this->faker->country,
            'current_country' => $this->faker->countryCode,
            'about' => $this->faker->text(300),
        ]);
        $profileResponse->assertJsonStructure([
            'data' => [
                'id',
                'user',
                'firstName',
                'lastName',
                'image',
                'birthday',
                'gender',
                'country',
                'currentCountry',
                'occupation',
                'experience',
                'japaneseLevel',
                'about',
                'isLivingInJapan',
                'isTOEIC',
                'isVisaObtained',
                'isLookingForLongTerm',
                'employmentStatus',
                'isVerified',
                'profileImg',
            ],
        ]);

        // Additional assertions if your API returns specific messages or status
        $profileResponse->assertJson([
            'success' => true,
            'message' => 'Jobseeker created successfully',
        ]);
    }
}
