<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Helpers\ApiResponse;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    public function getTasks()
    {
        $userId = auth()->user()->id;
        $tasks = Task::with(['members', 'tags'])->where([
            ['user_id', $userId],
        ])->get();

        $todoTasks = $tasks->filter(function ($task) {
            return $task->status === TaskStatus::TODO;
        });

        $inProgressTasks = $tasks->filter(function ($task) {
            return $task->status === TaskStatus::INPROGRESS;
        });

        $doneTasks = $tasks->filter(function ($task) {
            return $task->status === TaskStatus::DONE;
        });

        return ApiResponse::createSuccessResponse([
            'todo' => TaskResource::collection($todoTasks),
            'inProgress' => TaskResource::collection($inProgressTasks),
            'done' => TaskResource::collection($doneTasks),
        ]);
    }
}
