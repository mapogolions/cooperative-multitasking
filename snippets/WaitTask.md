```php
use Mapogolions\Multitask\{ Scheduler, Utils };
use Mapogolions\Multitask\System\{ GetTid, NewTask, WaitTask };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Debug;

function flow() {
  echo "start" . PHP_EOL;
  $tid1 = yield new NewTask(new DataProducer(Utils::countdown(3), new Debug()));
  yield new WaitTask($tid1);
  $tid2 = yield new NewTask(new DataProducer(Utils::countup(3), new Debug()));
  yield new WaitTask($tid2);
  echo "end" . PHP_EOL;
}

Scheduler::create()
  ->spawn(flow())
  ->launch();
```