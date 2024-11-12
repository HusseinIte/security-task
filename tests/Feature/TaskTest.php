<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskTest extends TestCase
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
    public function test_create_task_by_user_UnAuthorized()
    {
        $token = $this->authenticateAs($this->user);
        $response = $this->withToken(
            $token
        )->postJson('api/tasks', [
            "title" => "update Task",
            "type" => "Improvement",
            "description" => "nothing",
            "priority" => "High",
            "due_date" => "2024-10-22",
            "assigned_to" => 2
        ]);
        $response
            ->assertStatus(403)
            ->assertJson([
                'message' => 'Unauthorized access. Admin privileges are required.',
            ]);
    }

    public function test_create_task_by_admin()
    {
        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->postJson('api/tasks', [
            "title" => "update Task",
            "type" => "Improvement",
            "description" => "nothing",
            "priority" => "High",
            // Date must be greater than or equal to current day.
            "due_date" => "2024-11-12",
            "assigned_to" => 2
        ]);
        $response
            ->assertStatus(201)
            ->assertJson([
                'message' => 'Task created successfully',
            ]);
    }

    public function test_create_task_by_authorized_user_and_assign_to_user_not_found()
    {
        $token = $this->authenticateAs($this->admin);
        $response = $this->withToken(
            $token
        )->postJson('api/tasks', [
            "title" => "update Task",
            "type" => "Improvement",
            "description" => "nothing",
            "priority" => "High",
            // Date must be greater than or equal to current day.
            "due_date" => "2024-11-12",
            "assigned_to" => 5
        ]);
        $response
            ->assertStatus(422)
            ->assertJson([
                "message" => "Validation Exception",
            ]);
    }
}
