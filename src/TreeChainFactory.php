<?php
namespace GoogleTaxonomyHandler;

class TreeChainFactory extends AbstractChainFactory
{
    protected function build(): Tier
    {
        $result = new Tier(0, '');
        foreach ($this->raw as $line) {
            list($id, $tiers) = explode(' - ', $line, 2);
            $this->cast($result, $id, explode(' > ', trim($tiers)));
        }
        return $result->getChild();
    }

    private function cast(Tier $result, int $id, array $tiers)
    {
        if ($tier = array_shift($tiers)) {
            $current = $result;
            if ($current->hasChild()) {
                $current = $current->getChild();
                NEXT:
                if ($current->isSame($tier)) {
                    $this->cast($current, $id, $tiers);
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
                    $this->cast($current->getChild(), $id, $tiers);
                }
            }
        }
    }
}
