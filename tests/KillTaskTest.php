<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\{ SystemCall, GetTid, NewTask, KillTask };
use Mapogolions\Multitask\Spies\Repo;
use Mapogolions\Multitask\Suspendable\DataProducer;

class KillTaskTest extends TestCase
{
  public function testKillInfiniteLoopAfterTwoIterations()
  {
    $spy = new Repo();
    $suspendable = (function () use ($spy) {
      $tid = yield new GetTid();
      $childTid = yield new NewTask(new DataProducer(Utils::infiniteLoop(), $spy));
      for ($i = 0; $i < 2; $i++) {
        yield $tid;
      }
      yield new KillTask($childTid);
    })();

    $pl = new Scheduler();
    $pl
      ->spawn(new DataProducer($suspendable, $spy))
      ->launch();
    $this->assertEquals(
      ["<system call> GetTid", "<system call> NewTask", "<system call> GetTid", 1, 2, 1, 2, "<system call> KillTask"], 
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $spy->stock())
    );
  }
}
