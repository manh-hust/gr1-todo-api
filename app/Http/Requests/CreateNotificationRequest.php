<?php

namespace App\Http\Requests;

class CreateNotificationRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'content' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'content.required' => 'Content is required',
            'content.string' => 'Content must be a string',
        ];
    }
}
