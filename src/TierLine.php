<?php
namespace GoogleTaxonomyHandler;

class TierLine implements \IteratorAggregate
{
    private $line = [];

    public function append(Tier $tier): TierLine
    {
        $this->line[] = $tier;
        return $this;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->line);
    }
}
