<?php

namespace MyBuilder\Package\ToDo;

interface TaskRepository
{
    /**
     * @param Task $task
     *
     * @return void
     */
    public function store(Task $task);

    /**
     * @return Task[]
     */
    public function fetchAll();
}
