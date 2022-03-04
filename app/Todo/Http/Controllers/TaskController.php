<?php

namespace Todo\Http\Controllers;

use Todo\Http\Resources\TaskCollection;
use Todo\Contract\TaskInterface;
use Todo\Http\Resources\TaskResource;
use Todo\Infrustructure\BaseController;
use Todo\Model\Task;
use Todo\Notifications\TaskClosed;

class TaskController extends BaseController
{
    public function store()
    {
        $this->request->validate([
            'title' => 'required|max:150',
        ]);

        $requestData = $this->request->only(['title', 'description']);

        return Task::create([
            'title' => $requestData['title'],
            'user_id' => auth()->user()->id,
            'status' => TaskInterface::TASK_OPEN,
            'description' => $requestData['description'] ?? null
        ]);
    }

    public function update(Task $task)
    {
        $this->request->validate([
            'title' => 'required|max:150',
        ]);

        $requestData = $this->request->only(['title', 'description']);

        $task->title = $requestData['title'];
        $task->description = $requestData['description'] ?? null;

        $task->save();
    }

    public function changeStatus(Task $task)
    {
        $task->status = 1 - $task->status;

        if ($task->status == TaskInterface::TASK_CLOSED) $task->user->notify(new TaskClosed($task));

        $task->save();
    }

    public function updateLabels(Task $task)
    {
        $this->request->validate([
            'labels' => 'required|array'
        ]);

        $task->labels()->sync($this->request->labels);
    }

    public function index()
    {
        return new TaskCollection(Task::with('labels')->where('user_id', $this->request->user()->id)->get());
    }

    public function getTaskDetail($task)
    {
        $taskItem = Task::where('id', $task)->owned()->first();
        return $taskItem ? new TaskResource($taskItem) : [];
    }
}
