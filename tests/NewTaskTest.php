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
    $inner = TestKit::trackedAsDataProducer(TestKit::countdown(4), $spy);
    $outer = (function () use($inner) {
      $tid = yield new GetTid();
      yield $tid;
      $tid = yield new NewTask($inner);
    })();
    $pl = Scheduler::of(
      TestKit::trackedAsDataProducer($outer, $spy, TestKit::ignoreSystemCalls())
    );
    $pl->launch();
    $this->assertEquals([1, 4, 3, 2, 1], $spy->calls());
  }
}