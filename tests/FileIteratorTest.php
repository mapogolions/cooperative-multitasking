<?php

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Error\Error;
use org\bovigo\vfs\vfsStream;
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyCalls;
use Mapogolions\Multitask\System\{ SystemCall, FileIterator };

class FileIteratorTest extends TestCase
{
    private $source;
    private $spy;
    private $descriptor;

    public function setUp(): void
    {
        $this->source = vfsStream::newFile("tmp.txt")
            ->at(vfsStream::setup("home"))
            ->setContent(1 . PHP_EOL . 2 . PHP_EOL);
        $this->spy = new SpyCalls();
        $this->descriptor = \fopen($this->source->url(), "r");
    }

    public function tearDown(): void
    {
        if (is_resource($this->descriptor)) {
            \fclose($this->descriptor);
        }
    }

    private function flushedStream($descriptor)
    {
        $stream = yield new FileIterator($descriptor);
        yield from $stream;
    }

    private function notFlushedStream($descriptor)
    {
        $stream = yield new FileIterator($descriptor);
    }

    public function testNotFlushedStreamCanBeClosed()
    {
        Scheduler::create()
            ->spawn($this->notFlushedStream($this->descriptor))
            ->launch();

        $this->assertTrue(\fclose($this->descriptor));
    }

    public function testFlushedStreamCanNotBeClosed()
    {
        $this->expectException(Error::class);

        Scheduler::create()
            ->spawn($this->flushedStream($this->descriptor))
            ->launch();

        \fclose($this->descriptor);
    }

    public function testNotFlushedStreamDoesNotAchiveEndOfFile()
    {
        Scheduler::create()
            ->spawn($this->notFlushedStream($this->descriptor))
            ->launch();

        $this->assertFalse($this->source->eof());
    }

    public function testFlushedStreamAchivesTheEndOfTheFile()
    {
        Scheduler::create()
            ->spawn($this->flushedStream($this->descriptor))
            ->launch();

        $this->assertTrue($this->source->eof());
    }

    public function testNotFlushedStreamEmitsNothing()
    {
        Scheduler::create()
            ->spawn(new DataProducer($this->notFlushedStream($this->descriptor), $this->spy))
            ->launch();

        $this->assertEquals(
            ["<system call> FileIterator"],
            array_map(function ($it) {
                return $it instanceof SystemCall ? (string) $it : $it;
            }, $this->spy->calls())
        );
    }

    public function testFlushedStreamEmitsTheEntireContentsOfTheFile()
    {
        Scheduler::create()
            ->spawn(new DataProducer($this->flushedStream($this->descriptor), $this->spy))
            ->launch();

        $this->assertEquals(
            ["<system call> FileIterator", 1 . PHP_EOL, 2 . PHP_EOL],
            array_map(function ($it) {
                return $it instanceof SystemCall ? (string) $it : $it;
            }, $this->spy->calls())
        );
    }
}
