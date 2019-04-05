<?php
use PHPUnit\Framework\TestCase;
use Mapogolions\Multitask\{ Task, StopIteration, Utils };

final class TaskTest extends TestCase
{
  public function testTaskTidInitialization()
  {
    $task1 = new Task(1, Utils::countdown(2));
    $task2 = new Task(2, Utils::countdown(4));
    $this->assertSame($task1->tid(), 1);
    $this->assertSame($task2->tid(), 2);
  }

  public function testTaskDrain()
  {
    $suspendable = Utils::countdown(2);
    $task = new Task(1, $suspendable);
    $this->assertSame($task->run(), 2);
    $this->assertSame($task->run(), 1);
    $this->assertTrue($suspendable->valid());
  }

  public function testRaiseStopIterationWhenFullDrainHappens()
  {
    $this->expectException(StopIteration::class);
    $upperBound = 10;
    $task = new Task(2, Utils::countdown($upperBound));
    for ($i = $upperBound; $i > 0; $i--) {
      $task->run();
    }
    $task->run();
  }
}