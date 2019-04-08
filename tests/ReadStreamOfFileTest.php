<?php

use PHPUnit\Framework\TestCase;
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

  public function testNotFlushedStreamEmitsNothing()
  {
    $descriptor = \fopen($this->source->url(), "r");
    $suspendable = (function () use ($descriptor) {
      $stream = yield new ReadStreamOfFile($descriptor);
      // nothing do
    })();
    Scheduler::create()
      ->spawn(new DataProducer($suspendable, $this->spy))
      ->launch();

    $this->assertFalse($this->source->eof());
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
    $suspendable = (function () use ($descriptor) {
      $stream = yield new ReadStreamOfFile($descriptor);
      yield from $stream;
    })();
    Scheduler::create()
      ->spawn(new DataProducer($suspendable, $this->spy))
      ->launch();

    $this->assertTrue($this->source->eof());
    $this->assertEquals(
      ["<system call> ReadStreamOfFile", 1 . PHP_EOL, 2 . PHP_EOL],
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $this->spy->stock())
    );
  }
}