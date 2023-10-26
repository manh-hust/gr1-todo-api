<?php

namespace App\Http\Requests;

class ChangePasswordRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'oldPassword' => 'required|min:6|string',
            'newPassword' => 'required|min:6|confirmed|string',
        ];
    }

    public function messages()
    {
        return [
            'oldPassword.required' => 'Old password is required',
            'oldPassword.min' => 'Old password must be at least 6 characters',
            'oldPassword.string' => 'Old password must be a string',
            'newPassword.required' => 'New password is required',
            'newPassword.min' => 'New password must be at least 6 characters',
            'newPassword.confirmed' => 'New password confirmation does not match',
            'newPassword.string' => 'New password must be a string',
        ];
    }
}
