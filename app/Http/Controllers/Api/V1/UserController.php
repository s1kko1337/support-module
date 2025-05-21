<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\User\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(UpdateUserRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();
        $user->update($data);
        return response()->json([
            'user' => $user,
        ],200);
    }
}
