<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Api\UserResource;
use App\Http\Controllers\Api\UserController;
use App\Repositories\UserRepositoryInterface;
use Mockery;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;
    protected $userRepositoryMock;
    protected $controller;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userRepositoryMock = Mockery::mock(UserRepositoryInterface::class);
        $this->controller = new UserController($this->userRepositoryMock);
    }

    public function test_user_can_be_created()
    {
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'User',
        ];

        $user = User::create($userData);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($userData['name'], $user->name);
        $this->assertEquals($userData['email'], $user->email);
        $this->assertEquals($userData['role'], $user->role);
        $this->assertTrue(Hash::check($userData['password'], $user->password));
    }

    public function test_user_cannot_be_created_with_existing_email()
    {
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'role' => 'User',
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        User::create([
            'name' => 'Jane Doe',
            'email' => 'john@example.com',
            'password' => 'password456',
            'role' => 'User',
        ]);
    }

    public function test_user_can_be_found_by_email()
    {
        $user = User::factory()->create();

        $foundUser = User::where('email', $user->email)->first();

        $this->assertNotNull($foundUser);
        $this->assertEquals($user->id, $foundUser->id);
    }

    public function test_user_password_can_be_updated()
    {
        $user = User::factory()->create();
        $oldPassword = $user->password;

        $user->password = 'newpassword123';
        $user->save();

        $this->assertNotEquals($oldPassword, $user->password);
        $this->assertTrue(Hash::check('newpassword123', $user->password));
    }

    public function test_jwt_token_can_be_generated_for_user()
    {
        $user = User::factory()->create();

        $token = JWTAuth::fromUser($user);

        $this->assertIsString($token);
        $this->assertNotEmpty($token);
    }

    public function test_user_can_be_authenticated_with_correct_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $authenticated = auth()->attempt([
            'email' => 'test@example.com',
            'password' => 'correctpassword',
        ]);

        $this->assertTrue($authenticated);
    }

    public function test_user_cannot_be_authenticated_with_incorrect_credentials()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('correctpassword'),
        ]);

        $authenticated = auth()->attempt([
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertFalse($authenticated);
    }

    public function test_user_can_be_soft_deleted()
    {
        $user = User::factory()->create();
        $userId = $user->id;
        $user->delete();

        $this->assertDatabaseMissing('users', ['id' => $userId]);
        $this->assertNull(User::find($userId));
    }

    public function test_show_returns_user_when_found()
    {
        $user = User::factory()->create();
        $this->userRepositoryMock->shouldReceive('findById')
            ->with($user->id)
            ->andReturn($user);

        $response = $this->controller->show($user->id);

        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent());

        $this->assertIsObject($responseData->data);
        $this->assertEquals($user->id, $responseData->data->id);
        $this->assertEquals('Data retrieved successfully', $responseData->message);
    }

    public function test_show_returns_not_found_when_user_doesnt_exist()
    {
        $this->userRepositoryMock->shouldReceive('findById')
            ->with(999)
            ->andReturn(null);

        $response = $this->controller->show(999);

        $this->assertEquals(404, $response->getStatusCode());
        
        $responseData = json_decode($response->getContent());

        $this->assertEquals('User not found.', $responseData->error);
    }

    public function test_update_returns_updated_user_when_found()
    {
        $user = User::factory()->create();
        $newName = 'Updated Name'; 
        $this->userRepositoryMock->shouldReceive('findById')
            ->with($user->id)
            ->andReturn($user);

        $this->userRepositoryMock->shouldReceive('update')
            ->with($user, ['name' => $newName])
            ->andReturn(true);

        $request = new UpdateRequest();
        $request->merge(['name' => $newName]);

        $user->name = $newName;
        $user->save();

        $response = $this->controller->update($request, $user->id);

        $this->assertEquals(201, $response->getStatusCode());
        $responseData = json_decode($response->getContent());

        $this->assertIsObject($responseData->data);
        $this->assertEquals($newName, $responseData->data->name);
        $this->assertEquals('Data updated successfully', $responseData->message);
    }

    public function test_update_returns_not_found_when_user_doesnt_exist()
    {
        $this->userRepositoryMock->shouldReceive('findById')
            ->with(999)
            ->andReturn(null);
        $request = new UpdateRequest();
        $request->merge(['name' => 'New Name']);
        $response = $this->controller->update($request, 999);
        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('error', $responseData);
        $this->assertEquals('User not found.', $responseData['error']);
    }

}
