<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Http\Requests\ForgotPasswordRequest;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;
use App\Http\Services\AuthService;
use Illuminate\Support\Facades\Password;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    private $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user || !$this->authService->comparePassword($user->password, $request->password)) {
            return ApiResponse::createFailedResponse(['Username or password is incorrect']);
        }
        $token = $user->createToken('auth_token', ['user']);
        return ApiResponse::createSuccessResponse(['token' => $token->plainTextToken]);
    }

    public function adminLogin(Request $request)
    {
        $user = User::where('email', $request->email)->where('role', 1)->first();
        if (!$user || !Hash::check($request->password, $user->password)) {
            return ApiResponse::createFailedResponse(['Username or password is incorrect']);
        }
        $token = $user->createToken('auth_token', ['admin']);
        return ApiResponse::createSuccessResponse(['token' => $token->plainTextToken]);
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

    public function sendForgotPasswordEmail(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? ApiResponse::createSuccessResponse(null)
            : ApiResponse::createFailedResponse([__($status)]);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill(
                    [
                        'password' => Hash::make($request->password),
                    ]
                )->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? ApiResponse::createSuccessResponse(null)
            : ApiResponse::createFailedResponse([__($status)]);
    }

    public function changePassword(ChangePasswordRequest $request)
    {
        $userId = Auth::id();
        $user = User::findOrfail($userId);
        if (!$this->authService->comparePassword($user->password, $request->oldPassword)) {
            return ApiResponse::createFailedResponse(['Old password is incorrect']);
        }

        if ($this->authService->comparePassword($user->password, $request->newPassword)) {
            return ApiResponse::createFailedResponse(['New password must be different from old password']);
        }
        $user->password = Hash::make($request->newPassword);
        $user->save();
        return ApiResponse::createSuccessResponse(null);
    }
}
