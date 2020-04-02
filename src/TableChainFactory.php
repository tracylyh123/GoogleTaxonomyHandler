<?php
namespace GoogleTaxonomyHandler;

class TableChainFactory extends AbstractChainFactory
{
    protected function build(): Tier
    {
        $prehead1 = new Tier(0, '');
        $prev1 = $prehead1;
        foreach ($this->raw as $line) {
            list($id, $tiers) = explode(' - ', $line, 2);
            $prehead2 = new Tier(0, '');
            $prev2 = $prehead2;
            foreach (explode(' > ', $tiers) as $tier) {
                $current = new Tier($id, $tier);
                $prev2->setChild($current);
                $prev2 = $current;
            }
            $prev1->setNext($prehead2->getChild());
            $prev1 = $prehead2->getChild();
        }
        return $prehead1->getNext();
    }
}
