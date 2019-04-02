<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class SchedulerTest extends TestCase
{
  public function testSchedulerInitialState()
  {
    $pl = new Scheduler();
    $pl
      ->spawn(TestKit::countdown(10))
      ->spawn(TestKit::countup(10));
    $this->assertSame(2, count($pl->tasksPool()));
    $this->assertSame(0, count($pl->defferedTasksPool()));
  }

  public function testCountupToUpperBound()
  {
    $spy = new Spy();
    $pl = new Scheduler();
    $pl
      ->spawn(TestKit::trackedAsDataProducer(TestKit::countup(8), $spy))
      ->launch();
    $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $spy->calls());
  }

  public function testConcurrentExecutionOfTwoTasks()
  {
    $spy = new Spy();
    $pl = new Scheduler();
    $pl
      ->spawn(TestKit::trackedAsDataProducer(TestKit::countup(3), $spy))
      ->spawn(TestKit::trackedAsDataProducer(TestKit::countdown(6), $spy))
      ->launch();
    $this->assertEquals([1, 6, 2, 5, 3, 4, 3, 2, 1], $spy->calls());
  }
}