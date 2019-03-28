<?php
declare(strict_types=1);

namespace Mapogolions\Suspendable;

use Mapogolions\Suspendable\StopIteration;

class Task
{
  private static $total;
  private $id;
  private $suspendable;
  private $value = null;
  private $untracked = true;

  public function __construct($suspendable)
  {
    $this->id = ++self::$total;
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

  public function launch()
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

