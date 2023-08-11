<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !\Hash::check($request->password, $user->password)) {
            return ApiResponse::createFailedResponse(['The provided credentials are incorrect.']);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::createSuccessResponse(['token' => $token]);
    }
}
