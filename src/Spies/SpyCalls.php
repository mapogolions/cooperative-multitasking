<?php
namespace Mapogolions\Multitask\Spies;

use Mapogolions\Multitask\Spies\SpyInterface;

class SpyCalls implements SpyInterface
{
    private $repo = [];

    public function __invoke($data)
    {
        $this->repo[] = $data;
    }

    public function calls()
    {
        return $this->repo;
    }
}
