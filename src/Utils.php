<?php
namespace Mapogolions\Multitask;

use Mapogolions\Multitask\System\GetTid;

class Utils
{
  public static function countup(int $n): \Generator
  {
    $counter = 1;
    while ($counter <= $n) {
      yield $counter++;
    }
  }

  public static function countdown(int $n): \Generator
  {
    while ($n > 0) {
      yield $n--;
    }
  }

  public static function infiniteLoop(): \Generator
  {
    $tid = yield new GetTid();
    while (true) {
      yield $tid;
    }
  }

  public static function flush(\Iterator $suspendable)
  {
    foreach ($suspendable as $_) {}
  }
}
