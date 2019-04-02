<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ GetTid, NewTask, KillTask };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class KillTaskTest extends TestCase
{
  public function testKillInfiniteLoopAfterTwoIterations()
  {
    $spy = new Spy();
    $suspendable = (function () use ($spy) {
      $tid = yield new GetTid();
      $childTid = yield new NewTask(
        TestKit::trackedAsDataProducer(TestKit::infiniteLoop(), $spy, TestKit::ignoreSystemCalls())
      );
      for ($i = 0; $i < 2; $i++) {
        yield $tid;
      }
      yield new KillTask($childTid);
    })();

    $pl = new Scheduler();
    $pl
      ->spawn(
        TestKit::trackedAsDataProducer($suspendable, $spy, TestKit::ignoreSystemCalls())
      )
      ->launch();
    $this->assertEquals([1, 2, 1, 2], $spy->calls());
  }
}
