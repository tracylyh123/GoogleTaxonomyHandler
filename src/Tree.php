<?php
namespace GoogleTaxonomyHandler;

class Tree extends Node
{
    private $id;

    private $next;

    private $last;

    private $child;

    private $parent;

    private $isPruned = false;

    public function __construct(int $id, string $name)
    {
        parent::__construct($name);
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isEqual(Tree $tree): bool
    {
        return $tree->getId() === $this->id;
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

    public function setParent(Tree $tree): Tree
    {
        $this->parent = $tree;
        return $this;
    }

    public function getParent(): Tree
    {
        return $this->parent;
    }

    public function setLast(Tree $tree): Tree
    {
        $this->last = $tree;
        return $this;
    }

    public function getLast(): Tree
    {
        return $this->last;
    }

    public function hasChild(): bool
    {
        return !empty($this->child);
    }

    public function hasNext(): bool
    {
        return !empty($this->next);
    }

    public function hasParent(): bool
    {
        return !empty($this->parent);
    }

    public function hasLast(): bool
    {
        return !empty($this->last);
    }

    public function find(int $id): ?Tree
    {
        if ($this->isPruned()) {
            throw new \LogicException('node already been pruned');
        }
        return $this->_find($id, $this);
    }

    private function _find(int $id, Tree $tree): ?Tree
    {
        if ($tree->getId() === $id) {
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
        if ($tree->hasParent()) {
            $item['parent_id'] = $tree->getParent()->getId();
        }
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

    private function clearChild(): Tree
    {
        $this->child = null;
        return $this;
    }

    private function clearNext(): Tree
    {
        $this->next = null;
        return $this;
    }

    private function clearLast(): Tree
    {
        $this->last = null;
        return $this;
    }

    public function getRoot(): Tree
    {
        $root = $this;
        while ($root->hasParent()) {
            $root = $root->getParent();
        }
        return $root;
    }

    public function isRoot(): bool
    {
        return 0 === $this->getId();
    }

    public function prune()
    {
        if ($this->isRoot()) {
            throw new \LogicException('root node cannot be pruned');
        }
        $parent = $this->getParent();
        if ($this->isEqual($parent->getChild())) {
            if ($this->hasNext()) {
                $next = $this->getNext();
                $parent->setChild($next);
                $next->clearLast();
            } else {
                $parent->clearChild();
            }
        } else {
            if ($this->hasNext() && $this->hasLast()) {
                $next = $this->getNext();
                $last = $this->getLast();
                $next->setLast($last);
                $last->setNext($next);
            } elseif ($this->hasNext()) {
                $next = $this->getNext();
                $next->clearLast();
            } elseif ($this->hasLast()) {
                $last = $this->getLast();
                $last->clearNext();
            } else {
                throw new \LogicException('cannot delete this node');
            }
        }
        $this->isPruned = true;
    }

    public function isPruned(): bool
    {
        return $this->isPruned;
    }
}
