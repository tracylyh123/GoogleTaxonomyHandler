<?php
namespace Tests;

use GoogleTaxonomyHandler\TierBuilder;
use PHPUnit\Framework\TestCase;

class TierTreeTest extends TestCase
{
    public function testFind()
    {
        $builder = new TierBuilder();
        $builder->load([
            '1 - Animals & Pet Supplies',
            '3237 - Animals & Pet Supplies > Live Animals',
            '2 - Animals & Pet Supplies > Pet Supplies',
            '3 - Animals & Pet Supplies > Pet Supplies > Bird Supplies',
            '166 - Apparel & Accessories',
            '1604 - Apparel & Accessories > Clothing',
            '5322 - Apparel & Accessories > Clothing > Activewear',
            '5697 - Apparel & Accessories > Clothing > Activewear > Bicycle Activewear',
        ]);
        $tree = $builder->buildTree();

        $node = $tree->find(1);
        $this->assertNotNull($node);
        $this->assertTrue($node->hasChild());

        $node = $tree->find(3237);
        $this->assertNotNull($node);
        $this->assertEquals(3237, $node->getId());
        $this->assertEquals(2, $node->getNext()->getId());
        $this->assertTrue($node->getNext()->hasChild());
        $this->assertEquals(3, $node->getNext()->getChild()->getId());

        $node = $tree->find(166);
        $this->assertNotNull($node);
        $this->assertEquals(166, $node->getId());

        $node = $node->find(5322);
        $this->assertNotNull($node);
        $this->assertEquals(5322, $node->getId());
        $this->assertTrue($node->hasChild());
        $this->assertEquals(5697, $node->getChild()->getId());
    }

    public function testToArray()
    {
        $builder = new TierBuilder();
        $builder->load([
            '1 - Animals & Pet Supplies',
            '3237 - Animals & Pet Supplies > Live Animals',
            '2 - Animals & Pet Supplies > Pet Supplies',
            '3 - Animals & Pet Supplies > Pet Supplies > Bird Supplies',
            '166 - Apparel & Accessories',
            '1604 - Apparel & Accessories > Clothing',
            '5322 - Apparel & Accessories > Clothing > Activewear',
            '5697 - Apparel & Accessories > Clothing > Activewear > Bicycle Activewear',
        ]);
        $tree = $builder->buildTree();


    }
}
