<?php
declare(strict_types=1);

namespace Mapogolions\Coroutines;

abstract class SystemCall
{
  abstract public function handle(Task $task, Scheduler $scheduler): void;
}