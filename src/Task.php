<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

class Task
{
  private $id;
  private $suspendable;
  private $value = null;
  private $untracked = true;

  public function __construct($id, \Generator $suspendable)
  {
    $this->id = $id;
    $this->suspendable = $suspendable;
  }
  
  public function tid()
  {
    return $this->id;
  }

  public function getValue()
  {
    return $this->value;
  }
  public function setValue($value)
  {
    $this->value = $value;
  }

  public function run()
  {
    if ($this->untracked) {
      $this->untracked = false;
      return $this->suspendable->current();
    }
    if ($this->value instanceof StopIteration) {
      throw new StopIteration();
    }
    $this->suspendable->send($this->value);
    if (!$this->suspendable->valid()) {
      throw new StopIteration();
    }
    return $this->suspendable->current();
  }
}

