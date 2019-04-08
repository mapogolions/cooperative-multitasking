<?php

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\ReadFile;

class ReadFileTest extends TestCase
{
  private $root;
  private $dest;
  private $virtualDest;
  private $source;

  public function setUp(): void
  {
    $this->root = vfsStream::setup("home");
    $this->virtualDest = vfsStream::newFile("dest.txt")->at($this->root);
    $this->dest = \fopen($this->virtualDest->url(), "w+");
    $this->source = \fopen(
      vfsStream::newFile("source.txt")
        ->at($this->root)
        ->withContent(1 . PHP_EOL . 2 . PHP_EOL)
        ->url(),
      "r"
    );
  }

  public function tearDown(): void
  {
    if (\is_resource($this->source)) {
      \fclose($this->source);
    }
    if (\is_resource($this->dest)) {
      \fclose($this->dest);
    }
  }

  public function testReadFileContentToTheDest()
  {
    $suspendable = (function () {
      yield new ReadFile($this->source, $this->dest);
    })();
    Scheduler::create()
      ->spawn($suspendable)
      ->launch();

    $this->assertEquals(
      ["1", "2", ""],
      explode(PHP_EOL, $this->virtualDest->getContent())
    );
  }

  public function testAttemptToWriteIntoClosedFile()
  {
    $suspendable = (function () {
      yield new ReadFile($this->source, $this->dest);
      \fclose($this->dest);
    })();
    Scheduler::create()
      ->spawn($suspendable)
      ->launch();

    $this->assertNotEquals(
      ["1", "2", ""],
      explode(PHP_EOL, $this->virtualDest->getContent())
    );
  }
}