<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'startAt' => $this->start_at,
            'endAt' => $this->end_at,
            'updatedAt' => $this->updated_at->format('Y-m-d H:i:s'),
            'createdAt' => $this->created_at->format('Y-m-d H:i:s'),
            'members' => TaskMemberResource::collection($this->members),
            'tags' => TaskTagResource::collection($this->tags),
        ];
    }
}