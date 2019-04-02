```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ TimeSpan };


function timespan() {
  echo "start" . PHP_EOL;
  yield new TimeSpan(2);
  echo "end" . PHP_EOL;
}

$pl = Scheduler::of(timespan());
$pl->launch();
```