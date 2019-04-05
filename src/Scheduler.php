<?php
namespace Mapogolions\Multitask;

use Mapogolions\Multitask\System\SystemCall;

class Scheduler
{
  private $taskCount;
  private $ready;
  private $tasks;
  private $defferedTasks;
  
  public function __construct()
  {
    $this->taskCount = 0;
    $this->ready = new \SplQueue();
    $this->tasks = [];
    $this->defferedTasks = [];
  }

  public function suspendedTasksPool()
  {
    return $this->ready;
  }

  public function tasksPool()
  {
    return $this->tasks;
  }

  public function defferedTasksPool()
  {
    return $this->defferedTasks;
  }

  public function spawn($suspendable)
  {
    $task = new Task(++$this->taskCount, $suspendable);
    $this->tasks[$task->tid()] = $task;
    $this->schedule($task);
    return $this;
  }

  public function schedule(Task $task)
  {
    $this->ready->enqueue($task);
  }

  public function kill(Task $task)
  {
    unset($this->tasks[$task->tid()]);
    $defferedTasks = $this->defferedTasks[$task->tid()] ?? [];
    foreach ($defferedTasks as $defferedTask) {
      $this->schedule($defferedTask);
    }
    unset($this->defferedTasks[$task->tid()]);
    return --$this->taskCount;
  }

  public function waitForExit(Task $defferedTask, int $tid)
  {
    if (array_key_exists($tid, $this->tasks)) {
      $this->defferedTasks[$tid] = $this->defferedTasks[$tid] ?? [];
      array_push($this->defferedTasks[$tid], $defferedTask);
      return true;
    }
    return false;
  }

  public function launch()
  {
    while (count($this->tasks) > 0) {
      $task = $this->ready->dequeue();
      try {
        $exhaust = $task->run();
        if ($exhaust instanceof SystemCall) {
          $exhaust->handle($task, $this);
          continue;
        }
        $this->schedule($task);
      } catch (StopIteration $e) {
        $this->kill($task);
      }
    }
  }
}