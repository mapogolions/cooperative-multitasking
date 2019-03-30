<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

use Mapogolions\Suspendable\System\SystemCall;

class Scheduler
{
  private $taskCount;
  private $ready;
  private $tasks;

  public function __construct()
  {
    $this->taskCount = 0;
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

  public static function of(\Generator ... $suspendables) 
  {
    $pl = new Scheduler();
    foreach ($suspendables as $suspendable) {
      $pl->spawn($suspendable);
    }
    return $pl;
  }

  public function spawn(\Generator $suspendable)
  {
    $task = new Task(++$this->taskCount, $suspendable);
    $this->tasks[$task->tid()] = $task;
    $this->schedule($task);
    return $task->tid();
  }

  public function schedule(Task $task)
  {
    $this->ready->enqueue($task);
  }

  public function kill(Task $task)
  {
    echo "Task {$task->tid()} is terminated" . PHP_EOL;
    unset($this->tasks[$task->tid()]);
    return --$this->taskCount;
  }

  public function launch()
  {
    while (count($this->tasks) > 0) {
      $task = $this->ready->dequeue();
      try {
        $value = $task->run();
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