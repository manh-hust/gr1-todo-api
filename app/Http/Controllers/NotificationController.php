<?php

namespace App\Http\Controllers;

use App\Events\MyEvent;
use App\Helpers\ApiResponse;
use App\Http\Requests\CreateNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function getNotifications()
    {
        $notifications = Notification::all();
        return ApiResponse::createSuccessResponse(NotificationResource::collection($notifications));
    }

    public function createNotification(CreateNotificationRequest $request)
    {
        $user = auth()->user();

        if ($user->role != 1) {
            return ApiResponse::createFailedResponse(['You are not admin'], 403);
        }

        $notification = Notification::create([
            'title' => $request->title,
            'content' => $request->content,

        ]);

        MyEvent::dispatch();

        return ApiResponse::createSuccessResponse(new NotificationResource($notification));
    }
}