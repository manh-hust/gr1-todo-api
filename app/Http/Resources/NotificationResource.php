<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => $this?->image,
            "createdAt" => $this->created_at->format('Y-m-d H:i'),
            "updatedAt" => $this->updated_at->format('Y-m-d H:i'),
        ];
    }
}
