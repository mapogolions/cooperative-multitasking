#### STDOUT by default

```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ WaitTask, ReadFile };


function readSource() {
  $childTid = yield new ReadFile(__DIR__ . '/phpunit.xml', 'r');
  echo "Waiting for child" . PHP_EOL;
  yield new WaitTask($childTid);
  echo "Child done" . PHP_EOL;
}

$pl = Scheduler::of(readSource());
$pl->launch();
```

#### STDOUT - another file (config.xml)

```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ WaitTask, ReadFile };


function readSource() {
  $out = \fopen('config.xml', 'w+');
  $childTid = yield new ReadFile(__DIR__ . '/phpunit.xml', 'r', $out);
  echo "Waiting for child" . PHP_EOL;
  yield new WaitTask($childTid);
  \fclose($out);
  echo "Child done" . PHP_EOL;
}

$pl = Scheduler::of(readSource());
$pl->launch();
```