```php
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\NewTask;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyLog;

function flow() {
    yield new NewTask(new DataProducer(Utils::countdown(10), new SpyLog()));
    yield new NewTask(new DataProducer(Utils::countup(3), new SpyLog()));
}

Scheduler::create()
    ->spawn(flow())
    ->launch();
```
