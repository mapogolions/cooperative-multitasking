<?php
namespace Mapogolions\Multitask\Spies;

use Mapogolions\Multitask\Spies\SpyInterface;

final class Storage implements SpyInterface
{
  private $repo;

  public function __construct($repo=[])
  {
    $this->repo = $repo;
  }

  public function __invoke($data)
  {
    $this->repo[] = $data;
  }

  public function stock()
  {
    return $this->repo;
  }
}