<?php

namespace MyBuilder\App\ToDo\Cli;

use MyBuilder\Package\ToDo\Task;

class JsonFileTaskRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $filePath;
    private $tasks;

    public function setUp()
    {
        $this->filePath = __DIR__ . "/" . md5(time()) . ".json";
        $this->tasks = new JsonFileTaskRepository($this->filePath);
    }

    public function tearDown()
    {
        unlink($this->filePath);
    }

    /** 
     * @test
     */
    public function shouldStoreTask()
    {
        $this->tasks->store(new Task('Sample Title', 'Sample Description'));

        $this->assertContains('Sample Title', file_get_contents($this->filePath));
    }

    /**
     * @test
     */
    public function shouldFetchAllTasks()
    {
        $this->storeTasks($tasks = array(
            new Task('Title 1', 'Description 1'),
            new Task('Title 2', 'Description 2'),
        ));

        $this->assertEquals($tasks, $this->tasks->fetchAll());
    }

    private function storeTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            $this->tasks->store($task);
        }
    }
}
