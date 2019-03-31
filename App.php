<?php
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ GetTid, NewTask, KillTask, WaitTask };

function foo() {
  $tid = yield new GetTid();
  for ($i = 0; $i < 5; $i++) {
    echo "I'm foo " . $tid . PHP_EOL;
    yield;
  }
}

function main() {
  $child = yield new NewTask(foo());
  echo "Waiting for child" . PHP_EOL;
  yield new WaitTask($child);
  echo "Child done" . PHP_EOL;
}

$pl = Scheduler::of(main());
$pl->launch();