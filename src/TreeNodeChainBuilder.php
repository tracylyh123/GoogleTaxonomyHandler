<?php
namespace GoogleTaxonomyHandler;

class TreeNodeChainBuilder
{
    private $loaded = false;

    protected $raw = [];

    public function load(array $raw): TreeNodeChainBuilder
    {
        $this->raw = $raw;
        $this->loaded = true;

        return $this;
    }

    public function loadFromFile(string $file): TreeNodeChainBuilder
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

    public function build(): TreeNode
    {
        if (!$this->isLoaded()) {
            throw new \LogicException("no data loaded");
        }
        $result = new TreeNode(0, '');
        foreach ($this->raw as $line) {
            list($id, $tiers) = explode(' - ', $line, 2);
            $this->_build($result, $id, explode(' > ', trim($tiers)));
        }
        return $result->getChild();
    }

    private function _build(TreeNode $result, int $id, array $tiers)
    {
        if ($tier = array_shift($tiers)) {
            $current = $result;
            if ($current->hasChild()) {
                $current = $current->getChild();
                NEXT:
                if ($current->isSame($tier)) {
                    $this->_build($current, $id, $tiers);
                } else {
                    if ($current->hasNext()) {
                        $current = $current->getNext();
                        goto NEXT;
                    } else {
                        $current->setNext(new TreeNode($id, $tier));
                    }
                }
            } else {
                $current->setChild(new TreeNode($id, $tier));
                if ($tiers) {
                    $this->_build($current->getChild(), $id, $tiers);
                }
            }
        }
    }
}
