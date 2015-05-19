<?php

namespace MyBuilder\App\ToDo\Cli;

use MyBuilder\Package\ToDo\TaskService;

class App
{
    private $service;
    private $filePath;

    public function __construct($filePath)
    {
        $this->service = new TaskService(
            new JsonFileTaskRepository($this->filePath = $filePath)
        );
    }

    public function run()
    {
        $this->fillWithDummyTasks();

        foreach ($this->service->fetchAllTitles() as $title) {
            echo "* $title\n";
        }

        $this->removeAllTasks();
    }

    private function fillWithDummyTasks()
    {
        foreach (range(1, 3) as $n) {
            $this->service->addTask("Task $n", "Task $n Description");
        }
    }

    private function removeAllTasks()
    {
        unlink($this->filePath);
    }
}
