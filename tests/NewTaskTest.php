<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\{ SystemCall, GetTid, NewTask };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Storage;

class NewTaskTest extends TestCase
{
  public function testSequentialExecuctionOfTwoTasks()
  {
    $spy = new Storage();
    $derivedSuspendable = new DataProducer(Utils::countdown(4), $spy);
    $baseSuspendable = (function () use ($derivedSuspendable) {
      $tid = yield new GetTid();
      yield $tid;
      $childTid = yield new NewTask($derivedSuspendable);
    })();
    Scheduler::create()
      ->spawn(new DataProducer($baseSuspendable, $spy))
      ->launch();
    
    $this->assertEquals(
      ["<system call> GetTid", 1, "<system call> NewTask", 4, 3, 2, 1], 
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $spy->stock())
    );
  }
}