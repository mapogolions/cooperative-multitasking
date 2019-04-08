<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Error;
use org\bovigo\vfs\vfsStream;
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Storage;
use Mapogolions\Multitask\System\{ ReadStreamOfFile, SystemCall };

class ReadStreamOfFileTest extends TestCase
{
  private $source;
  private $spy;

  public function setUp(): void
  {
    $this->spy = new Storage();
    $this->source = vfsStream::newFile("tmp.txt")
      ->at(vfsStream::setup("home"))
      ->setContent(1 . PHP_EOL . 2 . PHP_EOL);
  }

  private function flushedStream($descriptor)
  {
    $stream = yield new ReadStreamOfFile($descriptor);
    yield from $stream;
  }

  private function notFlushedStream($descriptor)
  {
    $stream = yield new ReadStreamOfFile($descriptor);
  }

  public function testNotFlushedStreamCanBeClosed()
  {
    $descriptor = \fopen($this->source->url(), "r");
    Scheduler::create()
      ->spawn($this->notFlushedStream($descriptor))
      ->launch();

    $this->assertTrue(\fclose($descriptor));
  }

  public function testFlushedStreamCanNotBeClosed()
  {
    $this->expectException(Error::class);
    $descriptor = \fopen($this->source->url(), "r");
    Scheduler::create()
      ->spawn($this->flushedStream($descriptor))
      ->launch();

    \fclose($descriptor);
  }

  public function testNotFlushedStreamDoesNotAchiveEndOfFile()
  {
    $descriptor = \fopen($this->source->url(), "r");
    Scheduler::create()
      ->spawn($this->notFlushedStream($descriptor))
      ->launch();

    $this->assertFalse($this->source->eof());
  }

  public function testFlushedStreamAchivesEndOfFile()
  {
    $descriptor = \fopen($this->source->url(), "r");
    Scheduler::create()
      ->spawn($this->flushedStream($descriptor))
      ->launch();
    
    $this->assertTrue($this->source->eof());
  }

  public function testNotFlushedStreamEmitsNothing()
  {
    $descriptor = \fopen($this->source->url(), "r");
    Scheduler::create()
      ->spawn(new DataProducer($this->notFlushedStream($descriptor), $this->spy))
      ->launch();

    $this->assertEquals(
      ["<system call> ReadStreamOfFile"],
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $this->spy->stock())
    );
  }

  public function testFlushedStreamEmitsTheEntireContentsOfTheFile()
  {
    $descriptor = \fopen($this->source->url(), "r");
    Scheduler::create()
      ->spawn(new DataProducer($this->flushedStream($descriptor), $this->spy))
      ->launch();

    $this->assertEquals(
      ["<system call> ReadStreamOfFile", 1 . PHP_EOL, 2 . PHP_EOL],
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $this->spy->stock())
    );
  }
}