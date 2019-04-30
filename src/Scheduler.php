<?php
namespace Mapogolions\Multitask;

use Mapogolions\Multitask\System\SystemCall;

class Scheduler
{
    private $readyTasks = [];
    private $tasks = [];
    private $defferedTasks = [];

    public function __construct()
    {
        $this->readyTasks = new \SplQueue();
    }

    public static function create()
    {
        return new Scheduler();
    }

    public function readyPool()
    {
        return $this->readyTasks;
    }

    public function pool()
    {
        return $this->tasks;
    }

    public function defferedPool()
    {
        return $this->defferedTasks;
    }

    public function spawn($suspendable)
    {
        $task = new Task(count($this->tasks) + 1, $suspendable);
        $this->tasks[$task->tid()] = $task;
        $this->schedule($task);
        return $this;
    }

    public function schedule(Task $task)
    {
        $this->readyTasks->enqueue($task);
    }

    public function kill(Task $task)
    {
        unset($this->tasks[$task->tid()]);
        $defferedTasks = $this->defferedTasks[$task->tid()] ?? [];
        foreach ($defferedTasks as $defferedTask) {
            $this->schedule($defferedTask);
        }
        unset($this->defferedTasks[$task->tid()]);
        return count($this->tasks);
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
            $task = $this->readyTasks->dequeue();
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
