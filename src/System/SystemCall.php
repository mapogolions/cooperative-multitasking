<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\System;

use Mapogolions\Suspendable\{ Task, Scheduler };

abstract class SystemCall
{
  abstract public function handle(Task $task, Scheduler $scheduler): void;
}