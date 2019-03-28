<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\{ Task, Scheduler };

class GetTid extends SystemCall
{
  public function handle(Task $task, Scheduler $scheduler): void
  {
    $task->setValue($task->tid());
    $scheduler->schedule($task);
  }
}