<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::createFailedResponse(['Username or password is incorrect']);
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::createSuccessResponse(['token' => $token]);
    }

    public function register(RegisterRequest $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        $token = $user->createToken('auth_token')->plainTextToken;
        return ApiResponse::createSuccessResponse(['token' => $token]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return ApiResponse::createSuccessResponse([]);
    }

    public function redirectToGoogle()
    {
        $redirectUrl = Socialite::driver('google')->stateless()->redirect()->getTargetUrl();
        return ApiResponse::createSuccessResponse(['redirectUrl' => $redirectUrl]);
    }

    public function handleGoogleCallback()
    {
        $user = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $user->email],
            [
                'name' => $user->name,
                'password' => Hash::make($user->id),
                'google_id' => $user->id,
            ]
        );
        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::createSuccessResponse(['token' => $token]);
    }
}
