<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

class Scheduler
{
  private $ready;
  private $tasks;

  public function __construct()
  {
    $this->ready = new \SplQueue();
    $this->tasks = [];
  }

  public function queue()
  {
    return $this->ready;
  }

  public function pool()
  {
    return $this->tasks;
  }

  public function spawn($suspendable): int
  {
    $task = new Task($suspendable);
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
      try {
        $value = $task->launch();
        if ($value instanceof SystemCall) {
          $value->handle($task, $this);
          continue;
        }
        $this->schedule($task);
      } catch (StopIteration $e) {
        $this->kill($task);
      }
    }
  }
}