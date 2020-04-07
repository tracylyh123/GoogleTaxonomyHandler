<?php
namespace GoogleTaxonomyHandler;

class TierTree extends Tier
{
    private $next;

    private $child;

    public function setNext(TierTree $tier): TierTree
    {
        $this->next = $tier;
        return $this;
    }

    public function getNext(): TierTree
    {
        return $this->next;
    }

    public function setChild(TierTree $tier): TierTree
    {
        $this->child = $tier;
        return $this;
    }

    public function getChild(): TierTree
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

    public function find(int $id): ?TierTree
    {
        return $this->_find($id, $this);
    }

    private function _find(int $id, TierTree $tier): ?TierTree
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

    private function _toArray(TierTree $tier, bool $isResolve): array
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
            $item['childs'] = $this->_toArray($tier->getChild(), $isResolve);
        }
        $results[] = $item;
        if ($tier->hasNext()) {
            $tier = $tier->getNext();
            goto ADD;
        }
        return $results;
    }
}
