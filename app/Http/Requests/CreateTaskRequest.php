<?php

namespace App\Http\Requests;

class CreateTaskRequest extends BaseRequest
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
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'Title is required',
            'title.string' => 'Title must be a string',
            'description.string' => 'Description must be a string',
            'endAt.date' => 'EndAt must be a date',
            'tags.array' => 'Tags must be an array',
            'tags.*.integer' => 'Tags must be an array of integer',
            'members.array' => 'Members must be an array',
            'members.*.integer' => 'Members must be an array of integer',
        ];
    }
}
