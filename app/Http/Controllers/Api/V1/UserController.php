<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $user->update($request->toArray());
        return response()->json([
            'user' => $user,
        ],Response::HTTP_OK);
    }
}
