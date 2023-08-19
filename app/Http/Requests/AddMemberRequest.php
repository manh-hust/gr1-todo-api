<?php

namespace App\Http\Requests;

class AddMemberRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'members' => 'required|array',
            'members.*' => 'required|integer|exists:users,id',
        ];
    }

    public function messages()
    {
        return [
            'members.required' => 'Member id is required',
            'members.array' => 'Member id must be an array',
            'members.*.required' => 'Id must be an array of integer',
            'members.*.integer' => 'Id must be an array of integer',
            'members.*.exists' => 'Id must be an array of integer',
        ];
    }
}
