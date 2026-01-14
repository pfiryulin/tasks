<?php

namespace App\Http\Controllers;

use App\Models\Task;
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

    public function show(string $task) : array|Response
    {
        $task = Task::findOrFail($task)->load('responsible');
        if (!$task)
        {
            return response([
                'message' => 'Task not found',
            ], 404);
        }
//        dd($task);
        return [
            'id'             => $task->id,
            'title'          => $task->title,
            'description'    => $task->description,
            'deadline'       => $this->formateDate($task->deadline),
            'status'         => $task->status,
            'responsible' => $task->responsible->name,
        ];
    }

    public function store()
    {
        return 'Hello world';
    }

    protected function formateDate(Carbon $date) : string
    {
        return $date->format('d.m.Y');
    }
}
