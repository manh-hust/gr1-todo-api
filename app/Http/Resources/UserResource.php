<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'googleId' => $this->google_id,
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i'),
            'createdAt' => $this->created_at?->format('Y-m-d H:i'),
        ];
    }
}
