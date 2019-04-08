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
    $suspendable = (function () {
      $stream = yield new ReadStreamOfFile(\fopen($this->source->url(), "r"));
      // nothing do
    })();
    Scheduler::create()
      ->spawn(new DataProducer($suspendable, $this->spy))
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
    $suspendable = (function () {
      $stream = yield new ReadStreamOfFile(\fopen($this->source->url(), "r"));
      yield from $stream;
    })();
    Scheduler::create()
      ->spawn(new DataProducer($suspendable, $this->spy))
      ->launch();

    $this->assertEquals(
      ["<system call> ReadStreamOfFile", 1 . PHP_EOL, 2 . PHP_EOL],
      array_map(function ($it) {
        return $it instanceof SystemCall ? (string) $it : $it;
      }, $this->spy->stock())
    );
  }
}