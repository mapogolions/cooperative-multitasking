<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

class GetTid extends SystemCall
{
  public function handle(Task $task, Scheduler $scheduler): void
  {
    $task->setValue($task->tid());
    $scheduler->schedule($task);
  }
}