<?php

namespace App\Http\Requests;

class ResetPasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'email.exists' => 'Email does not exist',
            'token.required' => 'Token is required',
            'token.string' => 'Token must be a string',
            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'password.min' => 'Password must be at least 6 characters',
            'password.confirmed' => 'Password confirmation does not match',
        ];
    }
}
