```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\{ WaitTask, ReadFile };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyLog;

function flow() {
    $descriptor = \fopen(__DIR__ . '/phpunit.xml', 'r');
    $childTid = yield new ReadFile($descriptor);
    yield "before";
    yield new WaitTask($childTid);
    yield "after";
}

Scheduler::create()
    ->spawn(new DataProducer(flow(), new SpyLog(STDOUT)))
    ->launch();
```


```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\{ WaitTask, ReadFile };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyLog;

function flow() {
    $out = \fopen('settings.xml', 'w+');
    $childTid = yield new ReadFile(\fopen(__DIR__ . '/phpunit.xml', 'r'), $out);
    yield "Waiting for child";
    yield new WaitTask($childTid);
    \fclose($out);
    yield "Child done";
}

Scheduler::create()
    ->spawn(new DataProducer(flow(), new SpyLog(STDOUT)))
    ->launch();
```
