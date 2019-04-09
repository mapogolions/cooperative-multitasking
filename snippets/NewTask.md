```php
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\NewTask;
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Debug;

function flow() {
  yield new NewTask(new DataProducer(Utils::countdown(10), new Debug()));
  yield new NewTask(new DataProducer(Utils::countup(3), new Debug()));
}

Scheduler::create()
  ->spawn(flow())
  ->launch();
```