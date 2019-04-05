<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\{ SystemCall,  NewTask, WaitTask };
use Mapogolions\Multitask\Spies\{ Repo };
use Mapogolions\Multitask\Suspendable\DataProducer;

class WaitTaskTest extends TestCase
{
  public function testParentTaskWaitsForTerminatedDerivedTask()
  {
    $spy = new Repo();
    $suspendable = (function () use ($spy) {
      yield "start";
      $childTid = yield new NewTask(new DataProducer(Utils::countup(3), $spy));
      yield new WaitTask($childTid);
      yield "end";
    })();
    $pl = new Scheduler();
    $pl
      ->spawn(new DataProducer($suspendable, $spy))
      ->launch();
    $this->assertEquals([], $pl->defferedTasksPool());
    $this->assertEquals(
      ["start", "<system call> NewTask", 1, "<system call> WaitTask", 2, 3, "end"],
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $spy->stock())
    );    
  }
}