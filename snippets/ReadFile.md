```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ WaitTask, ReadFile };

function flow() {
  $childTid = yield new ReadFile(__DIR__ . '/phpunit.xml', 'r');
  echo "Waiting for child" . PHP_EOL;
  yield new WaitTask($childTid);
  echo "Child done" . PHP_EOL;
}

$pl = Scheduler::of(flow());
$pl->launch();
```

```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ WaitTask, ReadFile };

function flow() {
  $out = \fopen('config.xml', 'w+');
  $childTid = yield new ReadFile(__DIR__ . '/phpunit.xml', 'r', $out);
  echo "Waiting for child" . PHP_EOL;
  yield new WaitTask($childTid);
  \fclose($out);
  echo "Child done" . PHP_EOL;
}

$pl = Scheduler::of(flow());
$pl->launch();
```