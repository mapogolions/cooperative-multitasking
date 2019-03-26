<?php
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use Mapogolions\Coroutines\{ Scheduler, GetTid, NewTask };

function foo() {
  $tid = yield new GetTid();
  for ($i = 0; $i < 5; $i++) {
    echo "I'm foo " . $tid . PHP_EOL;
    yield;
  }
  $child = yield new NewTask(bar());
  echo "New task $child is created" . PHP_EOL;
}

function bar() {
  $tid = yield new GetTid();
  for ($i = 0; $i < 10; $i++) {
    echo "I'm bar " . $tid . PHP_EOL;
    yield;
  }
}


$sched = new Scheduler();
$sched->register(foo());
$sched->loop();