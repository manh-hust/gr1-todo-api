<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'string',
            'endAtDate' => 'date_format:Y-m-d',
            'endAtTime' => 'date_format:H:i',
            'tags' => 'array',
            'tags.*' => 'integer',
            'members' => 'array',
            'members.*' => 'integer',
            'status' => 'integer',
        ];
    }

    public function messages()
    {
        return [];
    }
}
