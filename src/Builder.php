<?php
namespace GoogleTaxonomyHandler;

class Builder
{
    private $loaded = false;

    private $raw = [];

    public function load(array $raw): Builder
    {
        $this->raw = $raw;
        $this->loaded = true;

        return $this;
    }

    public function loadFromFile(string $file): Builder
    {
        if (!file_exists($file)) {
            throw new \InvalidArgumentException("file: {$file} was not found");
        }
        $this->raw = file($file);
        $this->loaded = true;

        return $this;
    }

    public function isLoaded(): bool
    {
        return $this->loaded;
    }

    public function buildTree(): Tree
    {
        if (!$this->isLoaded()) {
            throw new \LogicException("no data loaded");
        }
        $root = new Tree(0, '');
        foreach ($this->raw as $line) {
            list($id, $tiers) = explode(' - ', $line, 2);
            $this->_buildTree($root, $id, explode(' > ', trim($tiers)));
        }
        return $root;
    }

    private function _buildTree(Tree $result, int $id, array $tiers)
    {
        if ($tier = array_shift($tiers)) {
            /**
             * @var $current Tree
             */
            $current = $result;
            if ($current->hasChild()) {
                $current = $current->getChild();
                NEXT:
                if ($current->isSame($tier)) {
                    $this->_buildTree($current, $id, $tiers);
                } else {
                    if ($current->hasNext()) {
                        $current = $current->getNext();
                        goto NEXT;
                    } else {
                        $next = new Tree($id, $tier);
                        $next->setLast($current);
                        if ($current->hasParent()) {
                            $next->setParent($current->getParent());
                        }
                        $current->setNext($next);
                    }
                }
            } else {
                $child = new Tree($id, $tier);
                $child->setParent($current);
                $current->setChild($child);
                if ($tiers) {
                    $this->_buildTree($current->getChild(), $id, $tiers);
                }
            }
        }
    }

    public function buildTable(): Table
    {
        if (!$this->isLoaded()) {
            throw new \LogicException("no data loaded");
        }
        $table = new Table();
        foreach ($this->raw as $line) {
            list($id, $tiers) = explode(' - ', $line, 2);
            $line = new Line($id);
            foreach (explode(' > ', trim($tiers)) as $tier) {
                $line->append(new Node($tier));
            }
            $table->append($line);
        }
        return $table;
    }
}
