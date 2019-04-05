<?php
namespace Mapogolions\Multitask\Suspendable;

interface SuspendablePartInterface extends \Iterator
{
  public function yields(): \Iterator;
}