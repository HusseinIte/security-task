<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a test user and log in to get the token
        $this->user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);
    }


    /**
     * test if user can login with valid credentails
     */
    public function test_a_user_can_log_in_with_valid_credentials(): void
    {

        // Act: Send a POST request to the login endpoint
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);


        $this->actingAs($this->user);
        // Assert: Check if the response is successful and includes a token
        $response->assertStatus(200);
    }

    /**
     * test if user can login with invalid credentails
     */
    public function test_a_user_can_log_in_with_in_valid_credentials(): void
    {

        // Act: Send a POST request to the login endpoint with invalid email
        $response = $this->postJson('/api/auth/login', [
            'email' => 'test2@example.com',
            'password' => 'password123',
        ]);

        // Assert: Check if the response is failed and return UnAuthenticated
        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.',
            ]);
    }
    /**
     *  Validation that the email address is entered in the standard format.
     */
    public function test_validation_email_for_login(): void
    {

        // Act: Send a POST request to the login endpoint with incorrect email
        $response = $this->postJson('/api/auth/login', [
            'email' => 'testexample.com',
            'password' => 'password123',
        ]);

        // Assert: Check if the response is failed and return Validation Error
        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => "Validation Error",
            ]);
    }
    public function test_logout()
    {

        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.access_token');
        // check if token is not null
        $this->assertNotNull($token, 'Token should not be null');
        $response = $this->withToken(
            $token
        )->postJson('api/auth/logout');
        $response->assertStatus(200);
    }
}
