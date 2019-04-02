<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Scheduler };
use Mapogolions\Suspendable\System\{ GetTid };
use Mapogolions\Suspendable\TestKit\{ TestKit, Spy };

class GetTidTest extends TestCase
{
  private $scheduler;
  private $spy;

  public function setUp(): void
  {
    $this->scheduler = new Scheduler();
    $this->spy = new Spy();
  }

  public function tearDown(): void
  {
    unset($this->scheduler);
    unset($this->spy);
  }

  public function testTaskIdentifier()
  {
    $suspandable = (function () {
      $tid = yield new GetTid();
      yield $tid;
      yield $tid;
    })();
    $this->scheduler
      ->spawn(
        TestKit::trackedAsDataProducer($suspandable, $this->spy, TestKit::ignoreSystemCalls())
      )
      ->launch();

    $this->assertEquals([1, 1], $this->spy->calls());
  }

  public function testTaskAsDataProducerWithoutSystemCalls()
  {
    $this->assertEquals($this->spy->calls(), []);
    $suspandable1 = (function () {
      $tid = yield new GetTid();
      yield $tid;
      yield $tid;
    })();
    $suspandable2 = (function () {
      $tid = yield new GetTid();
      yield $tid;
      yield $tid;
      yield $tid;
    })();
    $this->scheduler
      ->spawn(
        TestKit::trackedAsDataProducer($suspandable1, $this->spy, TestKit::ignoreSystemCalls())
      )
      ->spawn(
        TestKit::trackedAsDataProducer($suspandable2, $this->spy, TestKit::ignoreSystemCalls())
      )
      ->launch();

    $this->assertEquals([1, 2, 1, 2, 2], $this->spy->calls());
  }
}