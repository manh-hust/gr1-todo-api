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
}
