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

    public function handle(Task $task, Scheduler $scheduler)
    {
        $killedTask = $scheduler->pool()[$this->tid];
        if (isset($killedTask)) {
            $task->update(true);
            $killedTask->update(new StopIteration());
        } else {
            $task->update(false);
        }
        $scheduler->schedule($task);
    }

    public function __toString()
    {
        return "<system call> KillTask";
    }
}
