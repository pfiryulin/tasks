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
     * @param string $task - tasks id
     *
     * @return array|\Illuminate\Http\Response
     */
    public function show(string $id) : array|Response
    {
        $task = Task::findOrFail($id)->load('responsible');

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

    protected function formateDate(Carbon $date) : string
    {
        return $date->format('d.m.Y');
    }
}
