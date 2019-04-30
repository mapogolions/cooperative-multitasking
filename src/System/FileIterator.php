<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\System\{ SystemCall };
use Mapogolions\Multitask\{ Scheduler, Task, StopIteration };

final class FileIterator extends SystemCall
{
    private $descriptor;

    public function __construct($descriptor)
    {
        $this->descriptor = $descriptor;
    }

    private function readable()
    {
        if (!isset($this->descriptor)) {
            throw new StopIteration();
        }
        try {
            while (!\feof($this->descriptor)) {
                yield \fgets($this->descriptor);
            }
        } finally {
            \fclose($this->descriptor);
            return false;
        }
        return true;
    }

    public function handle(Task $task, Scheduler $scheduler): void
    {
        $task->setValue($this->readable());
        $scheduler->schedule($task);
    }

    public function __toString()
    {
        return "<system call> FileIterator";
    }
}
