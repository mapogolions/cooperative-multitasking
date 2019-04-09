```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\GetTid;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Debug;

function flow() {
  $tid = yield new GetTid();
  for ($i = 0; $i < 3; $i++)
    yield $tid;
}

Scheduler::create()
  ->spawn(new DataProducer(flow(), new Debug()))
  ->launch();
```