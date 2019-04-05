<?php
namespace Mapogolions\Multitask\Spies;

use Mapogolions\Multitask\Spies\Spy;

final class Debug implements Spy
{
  private $out;

  public function __construct($out=STDOUT)
  {
    $this->out = $out;
  }
  public function __invoke($data)
  {
    \fwrite($this->out, $data . PHP_EOL);
  }
}