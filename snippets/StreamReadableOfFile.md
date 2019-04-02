```php
use Mapogolions\Suspendable\Scheduler;
use Mapogolions\Suspendable\System\{ StreamReadableOfFile };

function streamReadable() {
  $stream = yield new StreamReadableOfFile(__DIR__ . '/phpunit.xml', 'r');
  echo "start" . PHP_EOL;
  foreach ($stream as $data) {
    echo $data;
    yield $data;
  }
  echo "end" . PHP_EOL;
}

$pl = Scheduler::of(streamReadable());
$pl->launch();
```