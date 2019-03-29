<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class TestScheduler extends TestCase
{
  public function testCountupToUpperBound()
  {
    $spy = new Spy();
    $pl = Scheduler::of(TestKit::trackedAsDataProducer(TestKit::countup(8), $spy));
    $pl->launch();
    $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $spy->calls());
  }

  public function testConcurrentExecutionOfTwoTasks()
  {
    $spy = new Spy();
    $pl = Scheduler::from([
      TestKit::trackedAsDataProducer(TestKit::countup(3), $spy),
      TestKit::trackedAsDataProducer(TestKit::countdown(6), $spy)
    ]);
    $pl->launch();
    $this->assertEquals([1, 6, 2, 5, 3, 4, 3, 2, 1], $spy->calls());
  }
}