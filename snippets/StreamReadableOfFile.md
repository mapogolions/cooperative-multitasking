### Deprecated

```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\StreamReadableOfFile;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Debug;

function flow() {
  $stream = yield new StreamReadableOfFile(__DIR__ . '/phpunit.xml', 'r');
  yield from $stream;
}

Scheduler::create()
  ->spawn(new DataProducer(flow(), new Debug()))
  ->launch();
```