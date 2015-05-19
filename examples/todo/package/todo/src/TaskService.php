<?php

namespace MyBuilder\Package\ToDo;

class TaskService
{
    private $tasks;

    public function __construct(TaskRepository $tasks)
    {
        $this->tasks = $tasks;
    }

    public function fetchAllTitles()
    {
        return array_map(function (Task $task) {
            return $task->getTitle();
        }, $this->tasks->fetchAll());
    }

    public function addTask($title, $description)
    {
        $this->tasks->store(new Task($title, $description));
    }
}
