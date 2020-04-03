<?php
namespace GoogleTaxonomyHandler;
require 'Tier.php';

class TreeChainFactory
{
    private $loaded = false;

    protected $raw = [];

    public function load(array $raw): TreeChainFactory
    {
        $this->raw = $raw;
        $this->loaded = true;

        return $this;
    }

    public function loadFromFile(string $file): TreeChainFactory
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

    public function build(): Tier
    {
        if (!$this->isLoaded()) {
            throw new \LogicException("no data loaded");
        }
        $result = new Tier(0, '');
        foreach ($this->raw as $line) {
            list($id, $tiers) = explode(' - ', $line, 2);
            $this->_build($result, $id, explode(' > ', trim($tiers)));
        }
        return $result->getChild();
    }

    private function _build(Tier $result, int $id, array $tiers)
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
                        $current->setNext(new Tier($id, $tier));
                    }
                }
            } else {
                $current->setChild(new Tier($id, $tier));
                if ($tiers) {
                    $this->_build($current->getChild(), $id, $tiers);
                }
            }
        }
    }
}

$a = new TreeChainFactory();
$b = $a->loadFromFile('../taxonomy-with-ids.en-US-20190710.txt')->build();
print_r($b->find(3391)->toArray());
