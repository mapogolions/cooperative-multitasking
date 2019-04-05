<?php
namespace Mapogolions\Multitask\Suspendable;

interface SuspendableRoleInterface extends \Iterator
{
  public function yields(): \Iterator;
}