<?php

use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\System\TimeSpan;
use Mapogolions\Multitask\Spies\SpyCalls;
use Mapogolions\Multitask\System\SystemCall;

class TimeSpanTest extends TestCase
{
    public function testExecutionFlowDelayForAWhile()
    {
        $suspendable = (function () {
            yield "start";
            yield new TimeSpan(0.1);
            yield "end";
        })();
        $spy = new SpyCalls();
        Scheduler::create()
            ->spawn(new DataProducer($suspendable, $spy))
            ->launch();

        $this->assertEquals(
            ["start", "<system call> TimeSpan(0.1)", "end"],
            array_map(function ($it) {
                return $it instanceof SystemCall ? (string) $it : $it;
            }, $spy->calls())
        );
    }
}
