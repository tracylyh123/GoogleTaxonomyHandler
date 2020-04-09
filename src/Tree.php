<?php
namespace GoogleTaxonomyHandler;

class Tree extends Node implements InterfaceTree
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
        $this->next = new NilTree();
        $this->last = new NilTree();
        $this->child = new NilTree();
        $this->parent = new NilTree();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function isEqual(InterfaceTree $tree): bool
    {
        return $tree->getId() === $this->id;
    }

    public function setNext(InterfaceTree $tree): InterfaceTree
    {
        $this->next = $tree;
        return $this;
    }

    public function getNext(): InterfaceTree
    {
        return $this->next;
    }

    public function setChild(InterfaceTree $tree): InterfaceTree
    {
        $this->child = $tree;
        return $this;
    }

    public function getChild(): InterfaceTree
    {
        return $this->child;
    }

    public function setParent(InterfaceTree $tree): InterfaceTree
    {
        $this->parent = $tree;
        return $this;
    }

    public function getParent(): InterfaceTree
    {
        return $this->parent;
    }

    public function setLast(InterfaceTree $tree): InterfaceTree
    {
        $this->last = $tree;
        return $this;
    }

    public function getLast(): InterfaceTree
    {
        return $this->last;
    }

    public function hasChild(): bool
    {
        return !$this->child->isNil();
    }

    public function hasNext(): bool
    {
        return !$this->next->isNil();
    }

    public function hasParent(): bool
    {
        return !$this->parent->isNil();
    }

    public function hasLast(): bool
    {
        return !$this->last->isNil();
    }

    public function find(int $id): InterfaceTree
    {
        if ($this->isPruned()) {
            throw new \LogicException('node already been pruned');
        }
        return $this->_find($id, $this);
    }

    private function _find(int $id, Tree $tree): InterfaceTree
    {
        if ($tree->getId() === $id) {
            return $tree;
        }

        if ($tree->hasNext()) {
            $next = $this->_find($id, $tree->getNext());
            if (!$next->isNil()) {
                return $next;
            }
        }

        if ($tree->hasChild()) {
            $child = $this->_find($id, $tree->getChild());
            if (!$child->isNil()) {
                return $child;
            }
        }

        return new NilTree();
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

    public function getRoot(): InterfaceTree
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
                $next->setLast(new NilTree());
            } else {
                $parent->setChild(new NilTree());
            }
        } else {
            if ($this->hasNext() && $this->hasLast()) {
                $next = $this->getNext();
                $last = $this->getLast();
                $next->setLast(new NilTree());
                $last->setNext($next);
            } elseif ($this->hasNext()) {
                $next = $this->getNext();
                $next->setLast(new NilTree());
            } elseif ($this->hasLast()) {
                $last = $this->getLast();
                $last->setNext(new NilTree());
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
