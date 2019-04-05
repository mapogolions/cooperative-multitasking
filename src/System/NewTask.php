<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\{ Task, Scheduler };

final class NewTask extends SystemCall
{
  private $suspendable;

  public function __construct($suspendable)
  {
    $this->suspendable = $suspendable;
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $scheduler->spawn($this->suspendable);
    $task->setValue(count($scheduler->tasksPool()));
    $scheduler->schedule($task);
  }

  public function __toString()
  {
    return "<system call> NewTask";
  }
}