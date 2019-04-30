<?php
namespace Mapogolions\Multitask;

use Mapogolions\Multitask\System\GetTid;

class Utils
{

    public static function countup(int $limit): \Generator
    {
        $counter = 1;
        while ($counter <= $limit) {
            yield $counter++;
        }
    }

    public static function countdown(int $limit): \Generator
    {
        while ($limit > 0) {
            yield $limit--;
        }
    }

    public static function infiniteLoop(): \Generator
    {
        $tid = yield new GetTid();
        while (true) {
            yield $tid;
        }
    }

    public static function flush(\Iterator $suspendable)
    {
        foreach ($suspendable as $_) {}
    }
}
