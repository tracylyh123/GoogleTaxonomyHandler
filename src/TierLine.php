<?php
namespace GoogleTaxonomyHandler;

class TierLine implements \IteratorAggregate, \ArrayAccess
{
    private $line = [];

    protected $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $offset
     * @return Tier
     */
    public function offsetGet($offset)
    {
        return $this->line[$offset] ?? null;
    }

    /**
     * @param int $offset
     * @param Tier $value
     */
    public function offsetSet($offset, $value)
    {
        if (isset($offset)) {
            $this->line[$offset] = $value;
        } else {
            $this->line[] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        return isset($this->line[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->line[$offset]);
    }

    public function append(Tier $node): TierLine
    {
        if (!$node->isEqual($this->id)) {
            throw new \InvalidArgumentException("id: {$node->getId()} should be {$this->id}");
        }
        $this->line[] = $node;
        return $this;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->line);
    }
}
