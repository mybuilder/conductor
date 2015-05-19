<?php

namespace MyBuilder\App\ToDo\Cli;

use MyBuilder\Package\ToDo\Task;
use MyBuilder\Package\ToDo\TaskRepository;

class JsonFileTaskRepository implements TaskRepository
{
    private $filePath;

    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    public function store(Task $task)
    {
        $tasks = $this->fetchTaskJson();

        $tasks[] = array(
            'title' => $task->getTitle(),
            'description' => $task->getDescription(),
        );

        file_put_contents($this->filePath, json_encode($tasks));
    }

    public function fetchAll()
    {
        return array_map(function ($task) {
            return new Task($task['title'], $task['description']);
        }, $this->fetchTaskJson());
    }

    private function fetchTaskJson()
    {
        if (file_exists($this->filePath)) {
            return json_decode(file_get_contents($this->filePath), true);
        }

        return array();
    }
}
