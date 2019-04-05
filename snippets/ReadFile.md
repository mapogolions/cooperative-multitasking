```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\{ WaitTask, ReadFile };

function flow() {
  $childTid = yield new ReadFile(__DIR__ . '/phpunit.xml', 'r');
  yield new WaitTask($childTid);
}

Scheduler::create()
  ->spawn(flow())
  ->launch();
```

```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\{ WaitTask, ReadFile };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Debug;

function flow() {
  $out = \fopen('settings.xml', 'w+');
  $childTid = yield new ReadFile(__DIR__ . '/phpunit.xml', 'r', $out);
  yield "Waiting for child";
  yield new WaitTask($childTid);
  \fclose($out);
  yield "Child done";
}

Scheduler::create()
  ->spawn(new DataProducer(flow(), new Debug(STDOUT)))
  ->launch();
```