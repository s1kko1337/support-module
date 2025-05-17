<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\Auth\LoginUserRequest;
use App\Http\Requests\Api\V1\StoreUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class AuthController extends Controller
{
    /**
     *  Регистрация нового пользователя
     *
     * @param StoreUserRequest $request
     * @return JsonResponse
     */
    public function register(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());
        $role = Role::findByName('curator');
        $user->assignRole($role);

        $permissions = $user->getAllPermissions()->pluck('name')->toArray();

        event(new Registered($user));
        return response()->json([
            'user' => $user,
            //'role' => $role,
            'token' => $user->createToken("Token of user: {$user->name}"/*$permissions*/)->plainTextToken,
        ]);

    }


    /**
     * Логин в приложении с предварительным удалением всех ранее созданных токенов.
     * Это гарантирует единственную онлайн сессию.
     * @param LoginUserRequest $request
     * @return JsonResponse
     */
    public function login(LoginUserRequest $request): JsonResponse
    {
        if (!Auth::attempt($request->validated())) {
            return response()->json([
                'errors' => 'Wrong email or password',
            ], 401);
        }

        $user = Auth::user();
        $permissions = $user->getAllPermissions()->pluck('name')->toArray();
        $user->tokens()->delete();
        return response()->json([
            'user' => $user,
            'token' => $user->createToken("Token of user: {$user->name}",$permissions)->plainTextToken,
        ]);
    }

    /**
     * Выход из приложения->удаление текущего токена
     *
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'Logged out, token removed',
        ]);
    }
}
