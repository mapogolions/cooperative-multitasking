<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

class KillTask extends SystemCall
{
  private $tid;

  public function __construct(int $tid)
  {
    $this->tid = $tid;
  }
  public function handle(Task $task, Scheduler $scheduler): void
  {
    $killedTask = $scheduler->pool()[$this->tid];
    if (isset($killedTask)) {
      $task->setValue(true);
      $killedTask->setValue(new StopIteration());
    } else {
      $task->setValue(false);
    }
    $scheduler->schedule($task);
  }
}