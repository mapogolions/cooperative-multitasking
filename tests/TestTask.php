<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Mapogolions\Suspendable\{ Task, StopIteration };

function countdown(int $n) {
  while ($n >= 0) {
    yield $n--;
  }
}

final class TestTask extends TestCase
{
  public function testTaskTidInitialization()
  {
    $task1 = new Task(countdown(2));
    $task2 = new Task(countdown(4));
    $this->assertSame($task1->tid(), 1);
    $this->assertSame($task2->tid(), 2);
  }

  public function testTaskDrain()
  {
    $upperBound = 1;
    $suspendable = countdown($upperBound);
    $task = new Task($suspendable);
    $this->assertSame($task->run(), 1);
    $this->assertSame($task->run(), 0);
    $this->assertTrue($suspendable->valid());
  }

  public function testRaiseStopIterationWhenFullDrainHappens()
  {
    $this->expectException(StopIteration::class);
    $upperBound = 10;
    $task = new Task(countdown($upperBound));
    for ($i = $upperBound; $i >= 0; $i--) {
      $task->run();
    }
    $task->run();
  }
}