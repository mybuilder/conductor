<?php

namespace MyBuilder\Package\ToDo;

class TaskServiceTest extends \PHPUnit_Framework_TestCase
{
    private $service;
    private $repository;

    public function setUp()
    {
        $this->service = new TaskService(
            $this->repository = new InMemoryTaskRepository
        );
    }

    /**
     * @test
     */
    public function shouldReturnTaskTitles()
    {
        $titles = $this->service->fetchAllTitles();

        $this->assertEquals(array('Task 1', 'Task 2', 'Task 3'), $titles);
    }

    /**
     * @test
     */
    public function shouldAddNewTask()
    {
        $this->service->addTask('Task 4', 'Task 4 Description');

        $this->assertContainsTask(
            new Task('Task 4', 'Task 4 Description'),
            $this->repository->fetchAll()
        );
    }

    private function assertContainsTask(Task $task, array $tasks)
    {
        foreach ($tasks as $t) {
            if ($t->equals($task)) {
                return true;
            }
        }

        $this->fail('Collection does not contain task');
    }
}

class InMemoryTaskRepository implements TaskRepository
{
    private $tasks;

    public function __construct()
    {
        $this->tasks = array(
            new Task('Task 1', 'Task 1 Description'),
            new Task('Task 2', 'Task 2 Description'),
            new Task('Task 3', 'Task 3 Description'),
        );
    }

    public function store(Task $task)
    {
        $this->tasks[] = $task;
    }

    public function fetchAll()
    {
        return $this->tasks;
    }
}
