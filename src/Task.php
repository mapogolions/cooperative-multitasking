<?php
declare(strict_types=1);

namespace Mapogolions\Coroutines;

class Task
{
  private static $total;
  private $id;
  private $suspendable;
  
  public function __construct($suspendable)
  {
    $this->id = ++self::$total;
    $this->suspendable = $suspendable;
    $this->value = null;
  }
  
  public function tid()
  {
    return $this->id;
  }

  public function message()
  {
    return $this->value;
  }

  public function valid(): bool
  {
    return $this->suspendable->valid();
  }

  public function current()
  {
    return $this->suspendable->current();
  }

  public function next()
  {
    $this->suspendable->next();
  }

  public function send($value): void
  {
    $this->value = $value;
    $this->suspendable->send($this->value);  
  }
}

