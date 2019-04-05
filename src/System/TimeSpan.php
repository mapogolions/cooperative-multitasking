<?php
namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\System\SystemCall;
use Mapogolions\Suspendable\{ Task, Scheduler };


final class TimeSpan extends SystemCall
{
  private $startTime;
  private $delay;

  public function __construct($delay)
  {
    $this->startTime = \time();
    $this->delay = $delay;  
  }

  public function handle(Task $defferedTask, Scheduler $scheduler): void
  {
    $timespan = (function () {
      while ($this->delay - (\time() - $this->startTime) > 0) {
        yield;
      }
    })();
    $scheduler->spawn($timespan);
    $status = $scheduler->waitForExit($defferedTask, count($scheduler->tasksPool()));
    $defferedTask->setValue($status);
  }

  public function __toString()
  {
    return "<system call> TimeSpan";
  }
}

