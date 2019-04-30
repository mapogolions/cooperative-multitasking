```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\FileIterator;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyLog;

function flow() {
    $descriptor = \fopen(__DIR__ . "/phpunit.xml", "r");
    $stream = yield new FileIterator($descriptor);
    yield from $stream;
}

Scheduler::create()
    ->spawn(new DataProducer(flow(), new SpyLog()))
    ->launch();
```
