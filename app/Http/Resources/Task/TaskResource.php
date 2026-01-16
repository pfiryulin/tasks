<?php

namespace App\Http\Resources\Task;

use App\Traits\TaskTrait;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Task
 */
class TaskResource extends JsonResource
{
    use TaskTrait;
    public function toArray(Request $request) : array
    {
        return [

            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'deadline'    => $this->formateDate($this->deadline),
            'status'      => $this->status,
            'responsible' => $this->responsible->name,
        ];
    }
}
