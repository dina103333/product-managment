<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Http\Requests\Api\RegisterUserRequest;
use App\Http\Requests\Api\LoginUserRequest;
use App\Http\Requests\Api\ResetPasswordRequest;
use App\Http\Requests\Api\UpdateRequest;
use App\Http\Resources\Api\UserResource;
use App\Traits\ApiResponse;


class UserController extends Controller
{
    use ApiResponse;

    public function register(RegisterUserRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'User',
        ]);
        $token = JWTAuth::fromUser($user);
        return $this->tokenResponse($token, 201);
    }

    public function login(LoginUserRequest $request)
    {
        $credentials = $request->only('email', 'password');
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return $this->errorResponse('Unauthorized' , 401);
            }
        } catch (JWTException $e) {
            return $this->errorResponse('Could not create token' , 500);
        }
        return $this->tokenResponse($token, 201);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return $this->notFoundResponse('User not found.');
        }
        $user->password = $request->password;
        $user->save();

        return $this->successResponse(UserResource::make($user),'Password reset successful', 201);
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);

            return $this->successResponse(UserResource::make($user),'Data retreved successfully', 201);
        } catch (\Exception $e) {
            return $this->notFoundResponse('User not found.');
        }
    }

    public function update(UpdateRequest $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            $user->update($request->all());

            return $this->successResponse(UserResource::make($user),'Data updated successfully', 201);
        } catch (\Exception $e) {
            return $this->notFoundResponse('User not found.');
        }
    }

    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return $this->successResponse(null,'User deleted successfully', 200);
        } catch (\Exception $e) {
            return $this->notFoundResponse('user not found.');
        }
    }
    
}
