<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable\TestKit;

use Mapogolions\Suspendable\TestKit\Spy;
use Mapogolions\Suspendable\System\SystemCall;

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

  public static function trackedAsDataProducer(\Generator $suspendable, Spy $spy, callable $predicate=null)
  {
    $predicate = $predicate ?? function ($value) { return true; };
    while ($suspendable->valid()) {
      $data = $suspendable->current();
      if ($predicate($data)) {
        $spy->apply($data);
      }
      $result = yield $data;
      $suspendable->send($result);
    }
  }

  public static function trackedAsDataConsumer(\Generator $suspendable, Spy $spy, callable $predicate=null)
  {
    $predicate = $predicate ?? function ($value) { return true; };
    while ($suspendable->valid()) {
      $item = yield $suspendable->current();
      if ($predicate($item)) {
        $spy->apply($item);
      }
      $suspendable->send($item);
    }
  }

  public static function ignoreSystemCalls() {
    return function ($value) {
      return $value instanceof SystemCall ? false : true;
    };
  }
}
