<?php
namespace GoogleTaxonomyHandler;

class Tree extends Node
{
    private $id;

    private $next;

    private $child;

    public function __construct(int $id, string $name)
    {
        parent::__construct($name);
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isEqual(int $id): bool
    {
        return $id === $this->id;
    }

    public function setNext(Tree $tree): Tree
    {
        $this->next = $tree;
        return $this;
    }

    public function getNext(): Tree
    {
        return $this->next;
    }

    public function setChild(Tree $tree): Tree
    {
        $this->child = $tree;
        return $this;
    }

    public function getChild(): Tree
    {
        return $this->child;
    }

    public function hasChild(): bool
    {
        return !empty($this->child);
    }

    public function hasNext(): bool
    {
        return !empty($this->next);
    }

    public function find(int $id): ?Tree
    {
        return $this->_find($id, $this);
    }

    private function _find(int $id, Tree $tree): ?Tree
    {
        if ($tree->isEqual($id)) {
            return $tree;
        }

        if ($tree->hasNext()) {
            $next = $this->_find($id, $tree->getNext());
            if ($next) {
                return $next;
            }
        }

        if ($tree->hasChild()) {
            $child = $this->_find($id, $tree->getChild());
            if ($child) {
                return $child;
            }
        }

        return null;
    }

    public function toArray(bool $isResolve = false): array
    {
        return $this->_toArray($this, $isResolve);
    }

    private function _toArray(Tree $tree, bool $isResolve): array
    {
        $results = [];
        ADD:
        $item = [
            'id' => $tree->getId(),
            'name' => $tree->getName(),
        ];
        if ($isResolve) {
            $item['resolved'] = $tree->resolve();
        }
        if ($tree->hasChild()) {
            $item['childs'] = $this->_toArray($tree->getChild(), $isResolve);
        }
        $results[] = $item;
        if ($tree->hasNext()) {
            $tree = $tree->getNext();
            goto ADD;
        }
        return $results;
    }
}
