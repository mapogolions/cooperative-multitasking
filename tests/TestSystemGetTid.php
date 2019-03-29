<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ GetTid };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class TestSystemGetTid extends TestCase
{
  public function testTaskIdentifier()
  {
    $suspandable = (function () {
      $tid = yield new GetTid();
      yield $tid;
      yield $tid;
    })();
    $spy = new Spy();
    $pl = Scheduler::of(
      TestKit::trackedAsDataProducer($suspandable, $spy, TestKit::ignoreSystemCalls())
    );
    $pl->launch();
    $this->assertEquals([1, 1], $spy->calls());
  }
}