<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ GetTid, NewTask, KillTask };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class KillTaskTest extends TestCase
{
  public function testFirstDraft()
  {
    $spy = new Spy();
    $outer = (function () use ($spy) {
      $tid = yield new GetTid();
      $child = yield new NewTask(
        TestKit::trackedAsDataProducer(TestKit::infiniteLoop(), $spy, TestKit::ignoreSystemCalls())
      );
      for ($i = 0; $i < 2; $i++) {
        yield $tid;
      }
      yield new KillTask($child);
    })();

    $pl = Scheduler::of(
      TestKit::trackedAsDataProducer($outer, $spy, TestKit::ignoreSystemCalls())
    );
    $pl->launch();
    $this->assertEquals([1, 2, 1, 2], $spy->calls());
  }
}
