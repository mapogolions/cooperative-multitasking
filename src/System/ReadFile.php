<?php

namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\System\SystemCall;
use Mapogolions\Multitask\{ Scheduler, Task, StopIteration };

final class ReadFile extends SystemCall
{
  private $descriptor;
  private $out;

  public function __construct($descriptor, $out=STDOUT)
  {
    $this->descriptor = $descriptor;
    $this->out = $out;
  }

  public function readable()
  {
    if (!isset($this->descriptor)) {
      throw new StopIteration();
    }
    try {
      while (!\feof($this->descriptor)) {
        $data = \fgets($this->descriptor);
        \fwrite($this->out, (string) $data);
        yield $data;
      }
    } catch (\Exception $e) {
      new StopIteration();
    } finally {
      \fclose($this->descriptor);
    }
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $scheduler->spawn($this->readable());
    $task->setValue(count($scheduler->tasksPool()));
    $scheduler->schedule($task);
  }

  public function __toString()
  {
    return "<system call> PrintFileContent";
  }
}