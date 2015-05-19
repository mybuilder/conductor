<?php

namespace MyBuilder\Package\ToDo;

class TaskTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function shouldCreateNewTask()
    {
        $task = new Task('Sample Title', 'Sample Description');

        $this->assertEquals('Sample Title', $task->getTitle());
        $this->assertEquals('Sample Description', $task->getDescription());
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldThrowForInvalidTitle()
    {
        $task = new Task('', 'Sample Description');
    }

    /**
     * @test
     * @expectedException \RuntimeException
     */
    public function shouldThrowForInvalidDescription()
    {
        $task = new Task('Sample Title', 'Foo');
    }

    /**
     * @test
     */
    public function shouldBeEqual()
    {
        $a = new Task('Task 1', 'Task 1 Description');
        $b = new Task('Task 1', 'Task 1 Description');

        $this->assertTrue($a->equals($b));
    }
}
