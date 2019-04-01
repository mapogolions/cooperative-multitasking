<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ NewTask, WaitTask };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class WaitTaskTest extends TestCase
{
  public function testParentTaskWaitsForTerminatedDerivedTask()
  {
    $spy = new Spy();
    $suspendable = (function () use ($spy) {
      yield "start";
      $child = yield new NewTask(
        TestKit::trackedAsDataProducer(TestKit::countup(3), $spy)
      );
      yield new WaitTask($child);
      yield "end";
    })();
    $pl = Scheduler::of(
      TestKit::trackedAsDataProducer($suspendable, $spy, TestKit::ignoreSystemCalls())
    );
    $pl->launch();
    $this->assertEquals(["start", 1, 2, 3, "end"], $spy->calls());
  }
}