<?php

namespace App\Http\Controllers;

use App\Http\Resources\Task\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use \Illuminate\Http\Response;

class TaskController extends Controller
{
    /**
     * Show tasks list
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index() : \Illuminate\Http\Resources\Json\AnonymousResourceCollection
    {
        $tasks = Task::query()->select([
            'id',
            'title',
            'description',
            'deadline',
            'status',
            'responsible_id',
        ])->with('responsible')->get();

//        return $tasks->map(fn(Task $task) => [
//            'id'          => $task->id,
//            'title'       => $task->title,
//            'description' => $task->description,
//            'deadline'    => $this->formateDate($task->deadline),
//            'status'      => $task->status,
//            'responsible' => $task->responsible->name,
//        ]);

        return TaskResource::collection($tasks);
    }

    /**
     * Show the task
     *
     * @param string $task - tasks id
     *
     * @return array|\Illuminate\Http\Response
     */
    public function show(string $id) : TaskResource | Response
    {
        $task = Task::where('id', $id)->with('responsible')->first();

        if (!$task)
        {
            return response([
                'message' => 'Task not found',
            ], 404);
        }

        return new TaskResource($task);
    }

    /**
     * Create task
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\Task
     */
    public function store(Request $request) : array | Response
    {
        $taskData = $request->validate([
            'title'          => ['required', 'string', 'min:3', 'max:255'],
            'description'    => ['required', 'string'],
            'deadline'       => ['date'],
            'responsible_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $newTask = Task::create($taskData);

        if (!$newTask)
        {
            return response([
                'message' => 'Task could not be created',
            ], 500);
        }

        return [
            'id'          => $newTask->id,
            'title'       => $newTask->title,
            'description' => $newTask->description,
            'deadline'    => $this->formateDate($newTask->deadline),
            'status'      => $newTask->status,
            'responsible' => $newTask->responsible->name,
        ];
    }

    /**
     * Update task
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $id
     *
     * @return \App\Models\Task|\Illuminate\Http\Response
     */
    public function update(Request $request, string $id) : array|Response
    {
        $task = Task::where('id', $id)->with('responsible')->first();

        if (!$task)
        {
            return response([
                'message' => 'Task not found',
            ], 404);
        }

        $taskData = $request->validate([
            'title'          => ['nullable', 'string', 'min:3', 'max:255'],
            'description'    => ['nullable', 'string'],
            'deadline'       => ['date'],
            'responsible_id' => ['nullable', 'integer', 'exists:users,id'],
        ]);
        $task->update($taskData);
        $task->refresh();

        return [
            'id'          => $task->id,
            'title'       => $task->title,
            'description' => $task->description,
            'deadline'    => $this->formateDate($task->deadline),
            'status'      => $task->status,
            'responsible' => $task->responsible->name,
        ];
    }

    /**
     * Delete the task
     *
     * @param string $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id) : Response
    {
        $task = Task::where('id', $id)->first();
        if (!$task)
        {
            return response([
                'message' => 'Task not found',
            ], 404);
        }

        $taskId = $task->id;
        $task->delete();

        return response([
            'message' => 'Task successfully deleted',
            'id'      => $taskId,
        ]);
    }

    /**
     *  Date formatting
     *
     * @param \Illuminate\Support\Carbon $date
     *
     * @return string
     */
    protected function formateDate(Carbon $date) : string
    {
        return $date->format('d.m.Y');
    }
}
