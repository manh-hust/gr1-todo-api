<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Helpers\ApiResponse;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use App\Models\TaskTag;
use Illuminate\Support\Facades\DB;
use App\Models\TaskMember;

class TaskController extends Controller
{
    public function getTasks()
    {
        // $userId = auth()->user()->id;
        $userId = 1;
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

    public function createTask(CreateTaskRequest $request)
    {
        // $userId = auth()->user()->id;
        $userId = 1;
        $endAt = $request->endAtDate . ' ' . $request->endAtTime;

        if ($endAt < now() && $endAt !== ' ') {
            return ApiResponse::createFailedResponse(['EndAt must be greater than now']);
        }

        DB::beginTransaction();
        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => now(),
                'end_at' => $endAt,
                'status' => TaskStatus::TODO,
                'user_id' => $userId,
            ]);

            if ($request->tags) {
                $insertData = collect($request->tags)->map(function ($tagId) use ($task) {
                    return [
                        'task_id' => $task->id,
                        'tag_id' => $tagId,
                    ];
                })->toArray();

                TaskTag::insert($insertData);
            }

            if ($request->members) {
                $insertData = collect($request->members)->map(function ($memberId) use ($task) {
                    return [
                        'task_id' => $task->id,
                        'user_id' => $memberId,
                    ];
                })->toArray();

                TaskMember::insert($insertData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::createFailedResponse([$e->getMessage()]);
        }

        return ApiResponse::createSuccessResponse(new TaskResource($task));
    }

    public function updateTask(UpdateTaskRequest $request, $id)
    {
        // $userId = auth()->user()->id;
        $userId = 1;
        $task = Task::where([
            ['id', $id],
            ['user_id', $userId],
        ])->first();

        if (!$task) {
            return ApiResponse::createFailedResponse(['Task not found']);
        }

        $endAt = $request->endAtDate . ' ' . $request->endAtTime;

        if ($endAt < now() && $endAt !== ' ') {
            return ApiResponse::createFailedResponse(['EndAt must be greater than now']);
        }

        DB::beginTransaction();
        try {
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'end_at' => $endAt,
                'status' => $request->status,
            ]);

            if ($request->tags) {
                $insertData = collect($request->tags)->map(function ($tagId) use ($task) {
                    return [
                        'task_id' => $task->id,
                        'tag_id' => $tagId,
                    ];
                })->toArray();

                TaskTag::where('task_id', $task->id)->delete();
                TaskTag::insert($insertData);
            }

            if ($request->members) {
                $insertData = collect($request->members)->map(function ($memberId) use ($task) {
                    return [
                        'task_id' => $task->id,
                        'user_id' => $memberId,
                    ];
                })->toArray();

                TaskMember::where('task_id', $task->id)->delete();
                TaskMember::insert($insertData);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::createFailedResponse([$e->getMessage()]);
        }

        return ApiResponse::createSuccessResponse(new TaskResource($task));
    }
}
