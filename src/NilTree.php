<?php
namespace GoogleTaxonomyHandler;

class NilTree extends NilNode implements InterfaceTree
{
    public function getId(): int
    {
        throw new \LogicException('cannot operate this node');
    }

    public function setNext(InterfaceTree $tree): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function getNext(): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function setChild(InterfaceTree $tree): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function getChild(): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function setParent(InterfaceTree $tree): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function getParent(): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function setLast(InterfaceTree $tree): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function getLast(): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function hasChild(): bool
    {
        throw new \LogicException('cannot operate this node');
    }

    public function hasNext(): bool
    {
        throw new \LogicException('cannot operate this node');
    }

    public function hasParent(): bool
    {
        throw new \LogicException('cannot operate this node');
    }

    public function hasLast(): bool
    {
        throw new \LogicException('cannot operate this node');
    }

    public function find(int $id): InterfaceTree
    {
        throw new \LogicException('cannot operate this node');
    }

    public function toArray(bool $isResolve = false): array
    {
        throw new \LogicException('cannot operate this node');
    }

    public function prune()
    {
        throw new \LogicException('cannot operate this node');
    }
}
