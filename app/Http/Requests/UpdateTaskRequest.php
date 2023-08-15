<?php

namespace App\Http\Requests;

class UpdateTaskRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => 'required|string',
            'description' => 'string',
            'startAt' => 'date_format:Y-m-d H:i',
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
            'title.string' => 'Title must be string',
            'description.string' => 'Description must be string',
            'startAt.date_format' => 'StartAt must be date format Y-m-d H:i',
            'tags.array' => 'Tags must be array',
            'tags.*.integer' => 'Tags must be integer',
            'members.array' => 'Members must be array',
            'members.*.integer' => 'Members must be integer',
        ];
    }
}
