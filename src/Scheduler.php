<?php
declare(strict_types=1);

namespace Mapogolions\Coroutines;

class Scheduler
{
  private $ready;
  private $tasks;

  public function __construct()
  {
    $this->ready = new \SplQueue();
    $this->tasks = [];
  }

  public function spawn($coroutine): int
  {
    $task = new Task($coroutine);
    $this->tasks[$task->tid()] = $task;
    $this->schedule($task);
    return $task->tid();
  }

  public function schedule(Task $task): void
  {
    $this->ready->enqueue($task);
  }

  public function kill(Task $task): void
  {
    echo "Task {$task->tid()} is terminated" . PHP_EOL;
    unset($this->tasks[$task->tid()]);
  }

  public function loop()
  {
    while (count($this->tasks) > 0) {
      $task = $this->ready->dequeue();
      if (!$task->valid()) {
        $this->kill($task);
        continue;
      }
      $result = $task->current();
      if ($result instanceof SystemCall) {
        $result->handle($task, $this);
        continue;
      }
      $task->next();
      $this->schedule($task);
    }
  }
}