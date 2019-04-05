<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\System\{ SystemCall };
use Mapogolions\Multitask\{ Scheduler, Task, StopIteration };

final class ReadFile extends SystemCall
{
  private $fileName;
  private $mode;
  private $out;

  public function __construct(string $fileName, string $mode="r", $out=STDOUT)
  {
    $this->fileName = $fileName;
    $this->mode = $mode;
    $this->out = $out;
  }

  private function readable()
  {
    $file = \fopen($this->fileName, $this->mode);
    if (!isset($file)) {
      throw new StopIteration();
    }
    try {
      while (!\feof($file)) {
        $data = \fread($file, 1024);
        \fwrite($this->out, $data);
        yield $data;
      }
    } finally {
      \fclose($file);
    }
    return true;
  }

  public function handle(Task $task, Scheduler $scheduler): void
  {
    $scheduler->spawn($this->readable());
    $task->setValue(count($scheduler->tasksPool()));
    $scheduler->schedule($task);
  }

  public function __toString()
  {
    return "<system call> ReadFile";
  }
}