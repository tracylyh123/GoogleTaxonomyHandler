<?php
namespace GoogleTaxonomyHandler;

class Line implements \IteratorAggregate, \ArrayAccess
{
    private $line = [];

    private $id;

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
     * @return Node
     */
    public function offsetGet($offset)
    {
        if (!isset($this->line[$offset])) {
            throw new \InvalidArgumentException("offset {$offset} was not found");
        }
        return $this->line[$offset];
    }

    /**
     * @param int $offset
     * @param Node $node
     */
    public function offsetSet($offset, $node)
    {
        if (isset($offset)) {
            $this->line[$offset] = $node;
        } else {
            $this->line[] = $node;
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

    public function append(Node $node): Line
    {
        $this->offsetSet(null, $node);
        return $this;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->line);
    }
}
