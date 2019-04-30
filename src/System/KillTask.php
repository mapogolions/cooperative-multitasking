<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\{ Task, Scheduler, StopIteration };

final class KillTask extends SystemCall
{
    private $tid;

    public function __construct(int $tid)
    {
        $this->tid = $tid;
    }

    public function handle(Task $task, Scheduler $scheduler): void
    {
        $killedTask = $scheduler->tasksPool()[$this->tid];
        if (isset($killedTask)) {
            $task->setValue(true);
            $killedTask->setValue(new StopIteration());
        } else {
            $task->setValue(false);
        }
        $scheduler->schedule($task);
    }

    public function __toString()
    {
        return "<system call> KillTask";
    }
}
