```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\{ WaitTask, PrintFileContent };

function flow() {
  $descriptor = \fopen(__DIR__ . '/phpunit.xml', 'r');
  $childTid = yield new PrintFileContent($descriptor);
  yield new WaitTask($childTid);
}

Scheduler::create()
  ->spawn(flow())
  ->launch();
```

```php
use Mapogolions\Multitask\Scheduler;
use Mapogolions\Multitask\System\{ WaitTask, PrintFileContent };
use Mapogolions\Multitask\Suspendable\DataProducer;
use Mapogolions\Multitask\Spies\Debug;

function flow() {
  $out = \fopen('settings.xml', 'w+');
  $childTid = yield new PrintFileContent(\fopen(__DIR__ . '/phpunit.xml', 'r'), $out);
  yield "Waiting for child";
  yield new WaitTask($childTid);
  \fclose($out);
  yield "Child done";
}

Scheduler::create()
  ->spawn(new DataProducer(flow(), new Debug(STDOUT)))
  ->launch();
```