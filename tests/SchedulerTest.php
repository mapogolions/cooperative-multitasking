<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Storage;

class SchedulerTest extends TestCase
{
  public function testSchedulerInitialState()
  {
    $pl = new Scheduler();
    $pl
      ->spawn(Utils::countdown(10))
      ->spawn(Utils::countup(10));
    $this->assertSame(2, count($pl->tasksPool()));
    $this->assertSame(0, count($pl->defferedTasksPool()));
  }

  public function testCountupToUpperBound()
  {
    $spy = new Storage();
    $pl = new Scheduler();
    $pl
      ->spawn(new DataProducer(Utils::countup(8), $spy))
      ->launch();
    $this->assertEquals([1, 2, 3, 4, 5, 6, 7, 8], $spy->stock());
  }

  public function testConcurrentExecutionOfTwoTasks()
  {
    $spy = new Storage();
    $pl = new Scheduler();
    $pl
      ->spawn(new DataProducer(Utils::countup(3), $spy))
      ->spawn(new DataProducer(Utils::countdown(6), $spy))
      ->launch();
    $this->assertEquals([1, 6, 2, 5, 3, 4, 3, 2, 1], $spy->stock());
  }
}