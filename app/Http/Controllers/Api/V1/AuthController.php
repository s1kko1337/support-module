<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreUserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    public function register(StoreUserRequest $request)
    {
        return User::create($request->validated());
    }
    public function login(LoginUserRequest $request)
    {
        if(!Auth::attempt($request->validated())) {
            return response()->json([
                'errors' => 'Wrong email or password',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'user' => $user,
            'token' => $user->createToken("Token of user: {$user->name}")->plainTextToken,
        ]);
    }

    public function logout()
    {

    }
}
