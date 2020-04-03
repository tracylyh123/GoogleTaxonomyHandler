<?php
namespace Tests;

use GoogleTaxonomyHandler\TierBuilder;
use GoogleTaxonomyHandler\TreeNode;
use PHPUnit\Framework\TestCase;

class TierBuilderTest extends TestCase
{
    public function testBuildTree()
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

        $this->assertTrue($tree instanceof TreeNode);

        $this->assertEquals(1, $tree->getId());
        $this->assertTrue($tree->hasChild());
        $this->assertTrue($tree->hasNext());

        $this->assertEquals(3237, $tree->getChild()->getId());
        $this->assertFalse($tree->getChild()->hasChild());
        $this->assertTrue($tree->getChild()->hasNext());

        $this->assertEquals(2, $tree->getChild()->getNext()->getId());
        $this->assertTrue($tree->getChild()->getNext()->hasChild());
        $this->assertFalse($tree->getChild()->getNext()->hasNext());

        $this->assertEquals(3, $tree->getChild()->getNext()->getChild()->getId());
        $this->assertFalse($tree->getChild()->getNext()->getChild()->hasNext());
        $this->assertFalse($tree->getChild()->getNext()->getChild()->hasChild());

        $this->assertEquals(166, $tree->getNext()->getId());
        $this->assertTrue($tree->getNext()->hasChild());
        $this->assertFalse($tree->getNext()->hasNext());

        $this->assertEquals(1604, $tree->getNext()->getChild()->getId());
        $this->assertTrue($tree->getNext()->getChild()->hasChild());
        $this->assertFalse($tree->getNext()->getChild()->hasNext());

        $this->assertEquals(5322, $tree->getNext()->getChild()->getChild()->getId());
        $this->assertTrue($tree->getNext()->getChild()->getChild()->hasChild());
        $this->assertFalse($tree->getNext()->getChild()->getChild()->hasNext());

        $this->assertEquals(5697, $tree->getNext()->getChild()->getChild()->getChild()->getId());
        $this->assertFalse($tree->getNext()->getChild()->getChild()->getChild()->hasChild());
        $this->assertFalse($tree->getNext()->getChild()->getChild()->getChild()->hasNext());
    }
}
