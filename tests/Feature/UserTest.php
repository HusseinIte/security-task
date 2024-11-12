<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    protected $user, $admin;
    protected function setUp(): void
    {

        parent::setUp();
        $this->seed();
        $this->admin = User::where('email', 'admin@example.com')->first();
        $this->user = User::where('email', 'user@example.com')->first();
    }
    public function test_example()
    {
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
        ]);
    }
    protected function authenticateAs($user)
    {
        $loginResponse = $this->postJson('/api/auth/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);
        $token = $loginResponse->json('data.access_token');
        $this->assertNotNull($token, 'Token should not be null');
        return $token;
    }
    public function test_create_user_from_unAuthorized_uesr()
    {

        $token = $this->authenticateAs($this->user);
        $response = $this->withToken(
            $token
        )->postJson('api/users', [
            'name'                  => 'test',
            'email'                 => 'test@example.com',
            'password'              => '123456789',
            'password_confirmation' => '123456789'
        ]);
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized access. Admin privileges are required.',
            ]);
    }
    public function test_create_user_from_Authorized_uesr()
    {

        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->postJson('api/users', [
            'name'                  => 'test',
            'email'                 => 'test@example.com',
            'password'              => '123456789',
            'password_confirmation' => '123456789'

        ]);
        $response->assertStatus(201);
    }
    public function test_create_user_with_password_mismatch()
    {
        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->postJson('api/users', [
            'name'                  => 'test',
            'email'                 => 'test@example.com',
            'password'              => '123456789',
            'password_confirmation' => '123456'

        ]);
        $response->assertStatus(422)
            ->assertJson([
                "message" => "Validation Error",
            ]);
    }

    public function test_show_user_not_found_in_database()
    {
        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->getJson('api/users/5');

        $response->assertStatus(404)
            ->assertJson([
                'message' => 'User Not Found',
            ]);
    }

    public function test_show_user_and_found_successfully_in_db()
    {
        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->getJson('api/users/2');

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'user has been retrieved successfully',
            ]);
    }

    public function test_if_user_has_been_soft_deleted()
    {

        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->deleteJson('api/users/2');
        $this->assertSoftDeleted($this->user);
        $response->assertStatus(200);
    }

    public function test_if_user_has_been_permanently_deleted()
    {

        $token = $this->authenticateAs($this->admin);
        $this->withToken(
            $token
        )->deleteJson('api/users/2');
        $this->assertSoftDeleted($this->user);
        $response = $this->withToken(
            $token
        )->deleteJson('api/users/2/force-delete');
        $this->assertModelMissing($this->user);
        $response->assertStatus(200);
    }
}
