<?php

namespace MyBuilder\Package\ToDo;

class Task
{
    private $title;
    private $description;

    public function __construct($title, $description)
    {
        $this->setTitle($title);
        $this->setDescription($description);
    }

    private function setTitle($title)
    {
        if ($title === '') {
            throw new \RuntimeException('Task title must be supplied.');
        }

        $this->title = $title;
    }

    private function setDescription($description)
    {
        if (strlen($description) < 5) {
            throw new \RuntimeException('Description must be greater than 4 chars long.');
        }

        $this->description = $description;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function equals(Task $that)
    {
        return
            $this->getTitle() === $that->getTitle() &&
            $this->getDescription() === $that->getDescription();
    }
}
