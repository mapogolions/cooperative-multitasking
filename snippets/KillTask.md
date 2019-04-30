```php
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\{ GetTid, NewTask, KillTask };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\SpyLog;

function flow() {
    $tid = yield new GetTid();
    $childTid = yield new NewTask(new DataProducer(Utils::infiniteLoop(), new SpyLog()));
    for ($i = 0; $i < 3; $i++)
        yield $tid;
    yield new KillTask($childTid);
}

Scheduler::create()
    ->spawn(new DataProducer(flow(), new SpyLog()))
    ->launch();
```
