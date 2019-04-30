<?php

namespace Mapogolions\Multitask;

class Task
{
    private $id;
    private $suspendable;
    private $data = null;
    private $untracked = true;

    public function __construct($id, $suspendable)
    {
        $this->id = $id;
        $this->suspendable = $suspendable;
    }

    public function tid()
    {
        return $this->id;
    }

    public function yield()
    {
        return $this->data;
    }

    public function update($data)
    {
        $this->data = $data;
    }

    public function run()
    {
        if ($this->untracked) {
            $this->untracked = false;
            return $this->suspendable->current();
        }
        if ($this->data instanceof StopIteration) {
            throw new StopIteration();
        }
        $this->suspendable->send($this->data);
        if (!$this->suspendable->valid()) {
            throw new StopIteration();
        }
        return $this->suspendable->current();
    }
}
