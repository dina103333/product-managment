<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserFeatureTest extends TestCase
{
    use RefreshDatabase;


    public function it_displays_user_data()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson("/api/users/{$user->id}");

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }


    public function it_updates_user_data()
    {
        $user = User::factory()->create();

        $updateData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ];

        $response = $this->actingAs($user, 'api')->putJson("/api/users/{$user->id}", $updateData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    }


    public function it_deletes_a_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }


    public function it_fails_to_show_non_existent_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'api')->getJson("/api/users/999");

        $response->assertStatus(404);
    }

    public function test_register_with_valid_data()
    {
        $response = $this->postJson('/api/users/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'token'
                 ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
        ]);
    }

    public function test_register_with_invalid_data()
    {
        $response = $this->postJson('/api/users/register', [
            'name' => '',
            'email' => 'invalid-email',
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'errors' => [
                    'message' => [
                        'name' => [
                            'The name field is required.'
                        ],
                        'email' => [
                            'The email field must be a valid email address.'
                        ],
                        'password' => [
                            'The password field must be at least 8 characters.',
                            'The password field confirmation does not match.'
                        ]
                    ]
                ]
            ]);
    }

    public function test_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make('password123'),
        ]);

        $response = $this->postJson('/api/users/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'token',
                 ]);
    }

    public function test_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/users/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertStatus(401)
                 ->assertJson([
                     'error' => 'Unauthorized',
                 ]);
    }

    public function test_reset_password_with_valid_email()
    {
        $user = User::factory()->create([
            'password' => Hash::make('oldpassword'),
        ]);

        $response = $this->postJson('/api/users/reset-password', [
            'email' => $user->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'message' => 'Password reset successful',
                 ]);
        $user->refresh();
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_reset_password_with_invalid_email()
    {
        $response = $this->postJson('/api/users/reset-password', [
            'email' => 'nonexistent@example.com',
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertStatus(404)
                 ->assertJson([
                     'error' => 'User not found.',
                 ]);
    }

    public function test_reset_password_with_invalid_password()
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/users/reset-password', [
            'email' => $user->email,
            'password' => 'short',
            'password_confirmation' => 'mismatch',
        ]);

        $response->assertStatus(422)
        ->assertJson([
            'errors' => [
                'message' => [
                    'password' => [
                        'The password field must be at least 8 characters.',
                        'The password field confirmation does not match.'
                    ]
                ]
            ]
        ]);
    }

    public function test_delete_user_that_does_not_exist()
    {
        $user = User::factory()->create();
        $response = $this->actingAs($user, 'api')->deleteJson('/api/users/9999');

        $response->assertStatus(404)
                ->assertJson([
                    'error' => 'user not found.',
                ]);
    }

    public function test_update_user_with_invalid_data()
    {
        $user = User::factory()->create(); 

        $response = $this->actingAs($user, 'api')->putJson("/api/users/{$user->id}", [
            'name' => '', 
            'email' => 'invalid-email', 
            'password' => 'short',
            'password_confirmation' => 'mismatch', 
        ]);

        $response->assertStatus(422)
                ->assertJson([
                    'errors' => [
                        'message' => [
                            'name' => [
                                'The name field is required.',
                            ],
                            'email' => [
                                'The email field must be a valid email address.',
                            ],
                            'password' => [
                                'The password field must be at least 8 characters.',
                                'The password field confirmation does not match.',
                            ],
                        ]
                    ]
                ]);
    }
}
