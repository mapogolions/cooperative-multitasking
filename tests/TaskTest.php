<?php
declare(strict_types=1);

use \PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
  public function testFirst(): string
  {
    $this->assertSame(1, 1);
    return "first";
  }
  /**
   * @depends testFirst
   */
  public function testSecond(string $value): void
  {
    $this->assertSame($value, "first");
  }
}