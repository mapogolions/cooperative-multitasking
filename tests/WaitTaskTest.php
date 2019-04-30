<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\{ SystemCall,  NewTask, WaitTask };
use Mapogolions\Multitask\Spies\{ Storage };
use Mapogolions\Multitask\Suspendable\DataProducer;

class WaitTaskTest extends TestCase
{
    public function testParentTaskWaitsForTerminatedDerivedTask()
    {
        $spy = new Storage();
        $suspendable = (function () use ($spy) {
            yield "start";
            $childTid = yield new NewTask(new DataProducer(Utils::countup(3), $spy));
            yield new WaitTask($childTid);
            yield "end";
        })();
        Scheduler::create()
            ->spawn(new DataProducer($suspendable, $spy))
            ->launch();

        $this->assertEquals(
            ["start", "<system call> NewTask", 1, "<system call> WaitTask", 2, 3, "end"],
            array_map(function ($it) {
                return $it instanceof SystemCall ? (string) $it : $it;
            }, $spy->stock())
        );
    }
}
