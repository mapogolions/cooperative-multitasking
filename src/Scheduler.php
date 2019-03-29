<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

use Mapogolions\Suspendable\System\SystemCall;

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

  public static function of(\Generator ... $suspendables) 
  {
    $pl = new Scheduler();
    foreach ($suspendables as $suspendable) {
      $pl->spawn($suspendable);
    }
    return $pl;
  }

  public static function from(array $suspandables)
  {
    $pl = new Scheduler();
    foreach ($suspandables as $suspandable) {
      $pl->spawn($suspandable);
    }
    return $pl;
  }

  public function spawn(\Generator $suspendable)
  {
    $task = new Task($suspendable);
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