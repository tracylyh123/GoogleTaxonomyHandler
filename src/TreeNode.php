<?php
namespace GoogleTaxonomyHandler;

class TreeNode extends Tier
{
    private $next;

    private $child;

    public function setNext(TreeNode $tier): TreeNode
    {
        $this->next = $tier;
        return $this;
    }

    public function getNext(): TreeNode
    {
        return $this->next;
    }

    public function setChild(TreeNode $tier): TreeNode
    {
        $this->child = $tier;
        return $this;
    }

    public function getChild(): TreeNode
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

    public function find(int $id): ?TreeNode
    {
        return $this->_find($id, $this);
    }

    private function _find(int $id, TreeNode $tier): ?TreeNode
    {
        if ($tier->isEqual($id)) {
            return $tier;
        }

        if ($tier->hasNext()) {
            $next = $this->_find($id, $tier->getNext());
            if ($next) {
                return $next;
            }
        }

        if ($tier->hasChild()) {
            $child = $this->_find($id, $tier->getChild());
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

    private function _toArray(TreeNode $tier, bool $isResolve): array
    {
        $results = [];
        ADD:
        $item = [
            'id' => $tier->getId(),
            'name' => $tier->getName(),
        ];
        if ($isResolve) {
            $item['resolved'] = $tier->resolve();
        }
        if ($tier->hasChild()) {
            $item['child'] = $this->_toArray($tier->getChild(), $isResolve);
        }
        $results[] = $item;
        if ($tier->hasNext()) {
            $tier = $tier->getNext();
            goto ADD;
        }
        return $results;
    }
}
