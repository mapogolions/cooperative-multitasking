<?php
declare(strict_types=1);

require_once __DIR__ . "/vendor/autoload.php";

use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\{ GetTid, NewTask, KillTask };


function foo() {
  $tid = yield new GetTid();
  for ($i = 0; $i < 5; $i++) {
    echo "I'm foo " . $tid . PHP_EOL;
    yield;
  }
  $child = yield new NewTask(bar());
  echo "New task $child is created " . PHP_EOL;
}


function bar() {
  $tid = yield new GetTid();
  for ($i = 0; $i < 10; $i++) {
    echo "I'm bar " . $tid . PHP_EOL;
    yield;
  }
  $child = yield new NewTask(spam());
  echo "New task $child is created " . PHP_EOL;
}

function spam() {
  $tid = yield new GetTid();
  $child = yield new NewTask(infinite_loop());
  echo "New task $child is created " . PHP_EOL;
  for ($i = 0; $i < 7; $i++) {
    echo "I'm spam " . $tid . PHP_EOL;
    yield;
  }
  yield new KillTask($child);
}

function infinite_loop() {
  $tid = yield new GetTid();
  while (true) {
    echo "I' am infinite loop " . $tid . PHP_EOL;
    yield;
  }
}

$sched = new Scheduler();
$sched->spawn(foo());
$sched->loop();