<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\System\SystemCall;
use Mapogolions\Suspendable\{ Task, Scheduler };


class TimeSpan extends SystemCall
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
    $tid = $scheduler->spawn($timespan);
    $status = $scheduler->waitForExit($defferedTask, $tid);
    $defferedTask->setValue($status);
  }
}
