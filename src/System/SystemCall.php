<?php
namespace Mapogolions\Multitask\System;

use Mapogolions\Multitask\{ Task, Scheduler };

abstract class SystemCall
{
  abstract public function handle(Task $task, Scheduler $scheduler): void;
}
