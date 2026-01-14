<?php

namespace App\Http\Controllers;

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
     * @return \Illuminate\Support\Collection
     */
    public function index() : Collection
    {
        $tasks = Task::query()->select([
            'id',
            'title',
            'description',
            'deadline',
            'status',
            'responsible_id',
        ])->with('responsible')->get();

        return $tasks->map(fn(Task $task) => [
            'id'             => $task->id,
            'title'          => $task->title,
            'description'    => $task->description,
            'deadline'       => $this->formateDate($task->deadline),
            'status'         => $task->status,
            'responsible' => $task->responsible->name,
        ]);
    }

    /**
     * Show the task
     * @param string $task - tasks id
     *
     * @return array|\Illuminate\Http\Response
     */
    public function show(string $id) : array | Response
    {
        $task = Task::where('id', $id)->with('responsible')->first();

        if (!$task)
        {
            return response([
                'message' => 'Task not found',
            ], 404);
        }

        return [
            'id'             => $task->id,
            'title'          => $task->title,
            'description'    => $task->description,
            'deadline'       => $this->formateDate($task->deadline),
            'status'         => $task->status,
            'responsible' => $task->responsible->name,
        ];
    }

    /**
     * Create task
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \App\Models\Task
     */
    public function store(Request $request) : Task
    {
        $taskData = $request->validate([
            'title'     => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['date'],
            'responsible_id' => ['required', 'integer', 'exists:users,id'],
        ]);

        $newTask = Task::create($taskData);

        return $newTask;
    }

    /**
     * Update task
     *
     * @param \Illuminate\Http\Request $request
     * @param string                   $id
     *
     * @return \App\Models\Task|\Illuminate\Http\Response
     */
    public function update(Request $request, string $id) : Task | Response
    {
        $task = Task::where('id', $id)->with('responsible')->first();

        if (!$task)
        {
            return response([
                'message' => 'Task not found',
            ], 404);
        }

        $taskData = $request->validate([
            'title'     => ['required', 'string', 'min:3', 'max:255'],
            'description' => ['required', 'string'],
            'deadline' => ['date'],
            'responsible_id' => ['required', 'integer', 'exists:users,id'],
        ]);
        $task->update($taskData);
        $task->refresh();

        return $task;
    }

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
            'id' => $taskId,
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
