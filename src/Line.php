<?php
namespace GoogleTaxonomyHandler;

class Line implements \IteratorAggregate, \ArrayAccess
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
     * @return Node
     */
    public function offsetGet($offset)
    {
        return $this->line[$offset] ?? null;
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
        $this->line[] = $node;
        return $this;
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->line);
    }
}
