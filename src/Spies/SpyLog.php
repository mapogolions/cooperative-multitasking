<?php
namespace Mapogolions\Multitask\Spies;

use Mapogolions\Multitask\Spies\SpyInterface;

class SpyLog implements SpyInterface
{
    private $out;

    public function __construct($out=STDOUT)
    {
        $this->out = $out;
    }

    public function __invoke($data)
    {
        \fwrite($this->out, (string) $data . PHP_EOL);
    }
}
