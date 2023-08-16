<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Helpers\ApiResponse;
use App\Http\Requests\CreateTaskRequest;
use App\Http\Requests\UpdateStatusRequest;
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
        $userId = auth()->user()->id;
        $tasks = Task::with(['members', 'tags'])->where([
            ['user_id', $userId],
            ['deleted_at', null]
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
                TaskMember::where('task_id', $task->id)->delete();
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

    public function updateStatus(UpdateStatusRequest $request, $id)
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

        switch ($request->status) {
            case TaskStatus::TODO:
                $task->update([
                    'status' => $request->status,
                    'end_at' => null,
                ]);
                break;
            case TaskStatus::INPROGRESS:
                $task->update([
                    'status' => $request->status,
                    'end_at' => null,
                ]);
                break;
            case TaskStatus::DONE:
                if (!$request->endAt) {
                    return ApiResponse::createFailedResponse(['EndAt is required']);
                }
                $task->update([
                    'status' => $request->status,
                    'end_at' => $request->endAt,
                ]);
                break;
            default:
                return ApiResponse::createFailedResponse(['Status is invalid']);
        }

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
}
