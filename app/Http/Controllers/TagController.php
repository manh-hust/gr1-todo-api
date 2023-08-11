<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Helpers\ApiResponse;
use App\Http\Resources\TagResource;

class TagController extends Controller
{
    public function getTags()
    {
        $tags = Tag::all();
        return ApiResponse::createSuccessResponse(TagResource::collection($tags));
    }
}
