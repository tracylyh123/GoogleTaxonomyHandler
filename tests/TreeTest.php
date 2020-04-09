<?php
namespace Tests;

use GoogleTaxonomyHandler\Builder;
use PHPUnit\Framework\TestCase;

class TreeTest extends TestCase
{
    public function testFind()
    {
        $builder = new Builder();
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
        $tree = $builder->buildTree()->getChild();

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
        $builder = new Builder();
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
        $tree = $builder->buildTree()->getChild();

        $array = $tree->toArray();
        $this->assertIsArray($array);
        $this->assertEquals(2, count($array));
        $this->assertEquals(1, $array[0]['id']);
        $this->assertArrayHasKey('childs', $array[0]);
        $this->assertEquals(2, count($array[0]['childs']));
        $this->assertEquals(3237, $array[0]['childs'][0]['id']);
        $this->assertEquals(2, $array[0]['childs'][1]['id']);
        $this->assertArrayHasKey('childs', $array[0]['childs'][1]);
        $this->assertEquals(1, count($array[0]['childs'][1]['childs']));
        $this->assertEquals(3, $array[0]['childs'][1]['childs'][0]['id']);
    }

    public function testPrune()
    {
        $builder = new Builder();
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

        $tree->find(2)->prune();
        $this->assertEquals(3237, $tree->getChild()->getChild()->getId());
        $this->assertFalse($tree->getChild()->getChild()->hasNext());

        $tree->find(1)->prune();
        $this->assertEquals(166, $tree->getChild()->getId());

        $tree->find(5322)->prune();
        $this->assertEquals(1604, $tree->getChild()->getChild()->getId());
        $this->assertFalse($tree->getChild()->getChild()->hasChild());

        $this->assertTrue($tree->find(2)->isNil());

        $this->expectException(\LogicException::class);
        $tree->find(0)->prune();
    }
}
