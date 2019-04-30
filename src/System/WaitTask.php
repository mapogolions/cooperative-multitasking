<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\System\SystemCall;
use Mapogolions\Multitask\{ Task, Scheduler };

final class WaitTask extends SystemCall
{
    private $tid;

    public function __construct(int $tid)
    {
        $this->tid = $tid;
    }

    public function handle(Task $defferedTask, Scheduler $scheduler): void
    {
        $status = $scheduler->waitForExit($defferedTask, $this->tid);
        $defferedTask->setValue($status);
        if (!$status) {
            $scheduler->schedule($defferedTask);
        }
    }

    public function __toString()
    {
        return "<system call> WaitTask";
    }
}
