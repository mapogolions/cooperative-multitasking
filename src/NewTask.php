<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

class NewTask extends SystemCall
{
  private $suspendable;

  public function __construct($suspendable)
  {
    $this->suspendable = $suspendable;
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $tid = $scheduler->spawn($this->suspendable);
    $task->setValue($tid);
    $scheduler->schedule($task);
  }
}