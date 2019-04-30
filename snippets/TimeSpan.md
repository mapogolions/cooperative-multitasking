```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\TimeSpan;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyLog;

function flow() {
    yield "start";
    yield new TimeSpan(2);
    yield "end";
}

Scheduler::create()
    ->spawn(new DataProducer(flow(), new SpyLog()))
    ->launch();
```
