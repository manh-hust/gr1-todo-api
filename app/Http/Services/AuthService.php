<?php

namespace App\Http\Services;

use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function comparePassword($hashedPassWord, $password)
    {
        return Hash::check($password, $hashedPassWord);
    }
}