<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\System\SystemCall;
use Mapogolions\Multitask\{ Task, Scheduler };

final class TimeSpan extends SystemCall
{
    private $startTime;
    private $delay;

    public function __construct($delay)
    {
        $this->startTime = \time();
        $this->delay = $delay;
    }

    public function handle(Task $defferedTask, Scheduler $scheduler)
    {
        $timespan = (function () {
            while ($this->delay - (\time() - $this->startTime) > 0) {
                yield;
            }
        })();
        $scheduler->spawn($timespan);
        $status = $scheduler->waitForExit($defferedTask, count($scheduler->pool()));
        $defferedTask->update($status);
    }

    public function __toString()
    {
        return "<system call> TimeSpan({$this->delay})";
    }
}
