<?php

namespace App\Http\Requests;

class RegisterRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|min:1|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|max:30|confirmed',
            'password_confirmation' => 'required|string|min:6|max:30'
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Name is required',
            'name.string' => 'Name must be a string',
            'name.min' => 'Name must be at least 1 character',
            'name.max' => 'Name must be at most 255 characters',

            'email.required' => 'Email is required',
            'email.email' => 'Email must be a valid email',
            'email.unique' => 'Email has already been taken',

            'password.required' => 'Password is required',
            'password.string' => 'Password must be a string',
            'password.min' => 'Password must be at least 6 characters',
            'password.max' => 'Password must be at most 30 characters',
            'password.confirmed' => 'Password confirmation does not match',

            'password_confirmation.required' => 'Password confirmation is required',
            'password_confirmation.string' => 'Password confirmation must be a string',
            'password_confirmation.min' => 'Password confirmation must be at least 6 characters',
            'password_confirmation.max' => 'Password confirmation must be at most 30 characters',
        ];
    }
}
