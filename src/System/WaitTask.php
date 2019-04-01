<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\System\SystemCall;
use Mapogolions\Suspendable\{ Task, Scheduler };

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
}