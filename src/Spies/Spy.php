<?php
namespace Mapogolions\Multitask\Spies;

interface Spy
{
  public function __invoke($data);
}