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

  public function testTaskAsDataProducerWithoutSystemCalls()
  {
    $spy = new Spy();
    $this->assertEquals($spy->calls(), []);
    $first = (function () {
      $tid = yield new GetTid();
      yield $tid;
      yield $tid;
    })();
    $second = (function () {
      $tid = yield new GetTid();
      yield $tid;
      yield $tid;
      yield $tid;
    })();
    $pl = Scheduler::of(
      TestKit::trackedAsDataProducer($first, $spy, TestKit::ignoreSystemCalls()),
      TestKit::trackedAsDataProducer($second, $spy, TestKit::ignoreSystemCalls())
    );
    $pl->launch();
    $this->assertEquals(
      [
        1, 2, 1, 2, 2
      ],
      $spy->calls()
    );
  }
}