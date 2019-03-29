<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\TestKit;

use Mapogolions\Suspendable\TestKit\Spy;

class TestKit
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

  public static function track(\Generator $suspendable, Spy $spy): \Generator
  {
    $items = self::arrayOfSuspendable($suspendable);
    return (function () use ($items, $spy) {
      foreach ($items as $item) {
        $spy->apply($item);
        yield $item;
      };
    })();
  }

  public static function arrayOfSuspendable(\Generator $suspendable): array
  {
    $items = [];
    foreach ($suspendable as $item) {
      $items[] = $item;
    }
    return $items;
  }
}