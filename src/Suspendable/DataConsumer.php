<?php
namespace Mapogolions\Multitask\Suspendable;

use Mapogolions\Multitask\Suspendable\SuspendablePartInterface;

class DataConsumer implements SuspendablePartInterface
{
  private $store = [];
  private $suspendable;
  private $spy;

  public function __construct(\Generator $suspendable, $spy=null)
  {
    $this->suspendable = $suspendable;
    $this->spy = $spy;
  }
  public function yields(): \Iterator
  {
    return new \ArrayIterator($this->store);
  }
  public function next()
  {
    return $this->suspendable->next();
  }
  public function current()
  {
    return $this->suspendable->current();
  }
  public function valid()
  {
    return $this->suspendable->valid();
  }
  public function send($data)
  {
    if (isset($this->spy)) {
      $spy = $this->spy;
      $spy($data);
    }
    $this->store[] = $data;
    return $this->suspendable->send($data);
  }
  public function key()
  {
    return $this->suspendable->key();
  }
  public function rewind()
  {
    return $this->suspendable->rewind();
  }
}