<?php
namespace Mapogolions\Multitask\Spies;

use Mapogolions\Multitask\Spies\Spy;

final class Repo implements Spy
{
  private $store;

  public function __construct($store=[])
  {
    $this->store = $store;
  }

  public function __invoke($data)
  {
    $this->store[] = $data;
  }

  public function stock()
  {
    return $this->store;
  }
}