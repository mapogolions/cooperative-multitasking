<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Scheduler };
use Mapogolions\Multitask\System\{ SystemCall, GetTid };
use Mapogolions\Multitask\Spies\SpyCalls;
use Mapogolions\Multitask\Suspendable\DataProducer;

class GetTidTest extends TestCase
{
    public function testTaskIdentifier()
    {
        $suspandable = (function () {
            $tid = yield new GetTid();
            yield $tid;
            yield $tid;
        })();
        $spy = new SpyCalls();
        Scheduler::create()
            ->spawn(new DataProducer($suspandable, $spy))
            ->launch();

        $this->assertEquals(
            ["<system call> GetTid", 1, 1],
            array_map(function ($it) {
                return $it instanceof SystemCall ? (string) $it : $it;
            }, $spy->calls())
        );
    }

    public function testTaskAsDataProducerWithoutSystemCalls()
    {
        $spy = new SpyCalls();
        $this->assertEquals($spy->calls(), []);
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
        Scheduler::create()
            ->spawn(new DataProducer($suspandable1, $spy))
            ->spawn(new DataProducer($suspandable2, $spy))
            ->launch();

        $this->assertEquals(
            ["<system call> GetTid", "<system call> GetTid", 1, 2, 1, 2, 2],
            array_map(function ($it) {
                return $it instanceof SystemCall ? (string) $it : $it;
            }, $spy->calls())
        );
    }
}
