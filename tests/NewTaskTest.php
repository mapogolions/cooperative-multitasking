<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ GetTid, NewTask };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class NewTaskTest extends TestCase
{
  public function testSequentialExecuctionOfTwoTasks()
  {
    $spy = new Spy();
    $derivedSuspendable = TestKit::trackedAsDataProducer(TestKit::countdown(4), $spy);
    $baseSuspendable = (function () use ($derivedSuspendable) {
      $tid = yield new GetTid();
      yield $tid;
      $childTid = yield new NewTask($derivedSuspendable);
    })();
    $pl = new Scheduler();
    $pl
      ->spawn(
        TestKit::trackedAsDataProducer($baseSuspendable, $spy, TestKit::ignoreSystemCalls())
      )
      ->launch();
    $this->assertEquals([1, 4, 3, 2, 1], $spy->calls());
  }

  public function testOverlappingBetweenTwoTasks()
  {
    $spy = new Spy();
    $derivedSuspendable= TestKit::trackedAsDataProducer(TestKit::countdown(5), $spy);
    $baseSuspendable = (function () use ($derivedSuspendable) {
      yield "start";
      $child = yield new NewTask($derivedSuspendable);
      yield "task $child is spawned";
      yield "end";
    })();
    $pl = new Scheduler();
    $pl
      ->spawn(
        TestKit::trackedAsDataProducer($baseSuspendable, $spy, TestKit::ignoreSystemCalls())  
      )
      ->launch();
    $this->assertEquals(
      ["start", 5, "task 2 is spawned", 4, "end", 3, 2, 1],
      $spy->calls()
    );
  }
}