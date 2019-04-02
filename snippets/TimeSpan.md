```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ TimeSpan };

function flow() {
  echo "start" . PHP_EOL;
  yield new TimeSpan(2);
  echo "end" . PHP_EOL;
}

$pl = Scheduler::of(flow());
$pl->launch();
```