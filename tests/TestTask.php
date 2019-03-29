<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Task, StopIteration };
use Mapogolions\Suspendable\TestKit\TestKit;

final class TestTask extends TestCase
{
  public function testTaskTidInitialization()
  {
    $task1 = new Task(TestKit::countdown(2));
    $task2 = new Task(TestKit::countdown(4));
    $this->assertSame($task1->tid(), 1);
    $this->assertSame($task2->tid(), 2);
  }

  public function testTaskDrain()
  {
    $upperBound = 2;
    $suspendable = TestKit::countdown($upperBound);
    $task = new Task($suspendable);
    $this->assertSame($task->run(), 2);
    $this->assertSame($task->run(), 1);
    $this->assertTrue($suspendable->valid());
  }

  public function testRaiseStopIterationWhenFullDrainHappens()
  {
    $this->expectException(StopIteration::class);
    $upperBound = 10;
    $task = new Task(TestKit::countdown($upperBound));
    for ($i = $upperBound; $i > 0; $i--) {
      $task->run();
    }
    $task->run();
  }
}