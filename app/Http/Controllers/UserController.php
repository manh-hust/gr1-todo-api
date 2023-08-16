<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Helpers\ApiResponse;
use App\Http\Resources\UserResource;

class UserController extends Controller
{
    public function getMembers()
    {
        $users = User::all();
        return ApiResponse::createSuccessResponse(UserResource::collection($users));
    }

    public function getUser()
    {
        $id = auth()->user()->id;
        $user = User::find($id);
        if (!$user) {
            return ApiResponse::createFailedResponse(['User not found']);
        }
        return ApiResponse::createSuccessResponse(new UserResource($user));
    }
}
