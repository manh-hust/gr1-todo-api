<?php

namespace App\Http\Requests;

class UpdateStatusRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'status' => 'required|integer',
            'endAt' => 'date_format:Y-m-d H:i',
        ];
    }

    public function messages()
    {
        return [
            'status.required' => 'Status is required',
            'status.integer' => 'Status must be integer',
            'endAt.date_format' => 'EndAt must be date format Y-m-d H:i',
        ];
    }
}
