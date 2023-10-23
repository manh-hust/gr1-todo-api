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
use App\Http\Requests\AddMemberRequest;

class TaskController extends Controller
{
    public function getTasks($type)
    {
        $userId = auth()->user()->id;
        $tasks = Task::with(['members', 'tags'])->where([
            ['user_id', $userId],
            ['deleted_at', null]
        ])->get();

        $todoTasks = collect([]);

        if ($type == 'todo') {
            $todoTasks = $tasks->filter(function ($task) {
                return $task->status == TaskStatus::TODO;
            });
        } else if ($type == 'done') {
            $todoTasks = $tasks->filter(function ($task) {
                return $task->status == TaskStatus::DONE;
            });
        }

        return ApiResponse::createSuccessResponse(TaskResource::collection($todoTasks));
    }

    public function getSharingTasks()
    {
        $userId = auth()->user()->id;
        $tasks = Task::with(['members', 'tags'])->whereHas('members', function ($query) use ($userId) {
            $query->where([
                ['user_id', $userId],
            ]);
        })->get();
        return ApiResponse::createSuccessResponse(TaskResource::collection($tasks));
    }

    public function createTask(CreateTaskRequest $request)
    {
        $userId = auth()->user()->id;

        DB::beginTransaction();
        try {
            $task = Task::create([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->startAt,
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
                TaskMember::insert([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'is_owner' => true,
                ]);
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
        $userId = auth()->user()->id;
        $task = Task::where([
            ['id', $id],
            ['user_id', $userId],
            ['deleted_at', null]
        ])->first();

        if (!$task) {
            return ApiResponse::createFailedResponse(['Task not found']);
        }

        DB::beginTransaction();
        try {
            $task->update([
                'title' => $request->title,
                'description' => $request->description,
                'start_at' => $request->startAt,
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
            } else {
                TaskTag::where('task_id', $task->id)->delete();
            }

            if ($request->members) {
                $insertData = collect($request->members)->map(function ($memberId) use ($task) {
                    return [
                        'task_id' => $task->id,
                        'user_id' => $memberId,
                    ];
                })->toArray();
                TaskMember::where([
                    ['task_id', $task->id],
                    ['user_id', '!=', $userId]
                ])->delete();
                TaskMember::insert($insertData);
            } else {
                TaskMember::where('task_id', $task->id)->delete();
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return ApiResponse::createFailedResponse([$e->getMessage()]);
        }

        return ApiResponse::createSuccessResponse(new TaskResource($task));
    }

    public function updateStatus($id)
    {
        $userId = auth()->user()->id;
        $task = Task::where([
            ['id', $id],
            ['user_id', $userId],
            ['deleted_at', null]
        ])->first();

        if (!$task) {
            return ApiResponse::createFailedResponse(['Task not found']);
        }

        $task->update([
            'status' => TaskStatus::DONE,
            'end_at' => date("Y-m-d H:i", strtotime('+7 hours')),
        ]);

        return ApiResponse::createSuccessResponse(new TaskResource($task));
    }

    public function deleteTask($id)
    {
        $userId = auth()->user()->id;
        $task = Task::where([
            ['id', $id],
            ['user_id', $userId],
            ['deleted_at', null]
        ])->first();

        if (!$task) {
            return ApiResponse::createFailedResponse(['Task not found']);
        }

        $task->update([
            'deleted_at' => now(),
        ]);

        return ApiResponse::createSuccessResponse([]);
    }

    public function addMember(AddMemberRequest $request, $id)
    {
        $userId = auth()->user()->id;
        $task = Task::where([
            ['id', $id],
            ['deleted_at', null]
        ])->first();

        if (!$task) {
            return ApiResponse::createFailedResponse(['Task not found']);
        }

        $insertData = collect($request->members)->map(function ($memberId) use ($task) {
            return [
                'task_id' => $task->id,
                'user_id' => $memberId,
            ];
        })->toArray();

        TaskMember::where([
            ['task_id', $task->id],
        ])->delete();

        TaskMember::insert($insertData);

        TaskMember::insert([
            'task_id' => $task->id,
            'user_id' => $userId,
            'is_owner' => true,
        ]);

        return ApiResponse::createSuccessResponse([]);
    }
}
