<?php
declare(strict_types=1);

namespace Mapogolions\Coroutines;

class Task
{
  private static $total;
  private $id;
  private $coroutine;
  
  public function __construct($coroutine)
  {
    $this->id = ++self::$total;
    $this->coroutine = $coroutine;
    $this->value = null;
  }
  
  public function tid()
  {
    return $this->id;
  }

  public function suspendable()
  {
    return $this->coroutine;
  }

  public function message()
  {
    return $this->value;
  }

  public function valid(): bool
  {
    return $this->coroutine->valid();
  }

  public function current()
  {
    return $this->coroutine->current();
  }

  public function next()
  {
    $this->coroutine->next();
  }

  public function send($value): void
  {
    $this->value = $value;
    $this->coroutine->send($this->value);  
  }
}

