<?php
namespace Mapogolions\Multitask\Suspendable;

interface SuspendableInterface extends \Iterator
{
    public function yields(): \Iterator;
}
