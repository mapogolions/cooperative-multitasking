<?php
declare(strict_types=1);

namespace Mapogolions\Coroutines;


class GetTid extends SystemCall
{
  public function handle(Task $task, Scheduler $scheduler): void
  {
    $task->send($task->tid());
    $scheduler->schedule($task);
  }
  
  public function __toString()
  {
    return "<system call> GetTid";
  }
}