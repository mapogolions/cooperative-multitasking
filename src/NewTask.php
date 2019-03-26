<?php
declare(strict_types=1);

namespace Mapogolions\Coroutines;

class NewTask extends SystemCall
{
  private $generator;

  public function __construct($generator)
  {
    $this->generator = $generator;
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $tid = $scheduler->register($this->generator);
    $task->send($tid);
    $scheduler->schedule($task);
  }
}