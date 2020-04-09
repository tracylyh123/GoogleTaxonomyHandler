<?php
namespace Tests;

use GoogleTaxonomyHandler\Builder;
use GoogleTaxonomyHandler\Line;
use GoogleTaxonomyHandler\Table;
use GoogleTaxonomyHandler\Tree;
use PHPUnit\Framework\TestCase;

class BuilderTest extends TestCase
{
    public function testBuildTree()
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

        $this->assertTrue($tree instanceof Tree);

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

    public function testBuildTable()
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
        $table = $builder->buildTable();

        $this->assertTrue($table instanceof Table);

        /**
         * @var $table Line[]
         */
        $this->assertEquals(1, $table[1]->getId());
        $this->assertTrue($table[1][0]->isSame('Animals & Pet Supplies'));

        $this->assertEquals(3237, $table[3237]->getId());
        $this->assertTrue($table[3237][0]->isSame('Animals & Pet Supplies'));
        $this->assertTrue($table[3237][1]->isSame('Live Animals'));

        $this->assertEquals(2, $table[2]->getId());
        $this->assertTrue($table[2][0]->isSame('Animals & Pet Supplies'));
        $this->assertTrue($table[2][1]->isSame('Pet Supplies'));

        $this->assertEquals(3, $table[3]->getId());
        $this->assertTrue($table[3][0]->isSame('Animals & Pet Supplies'));
        $this->assertTrue($table[3][1]->isSame('Pet Supplies'));
        $this->assertTrue($table[3][2]->isSame('Bird Supplies'));

        $this->assertEquals(166, $table[166]->getId());
        $this->assertTrue($table[166][0]->isSame('Apparel & Accessories'));

        $this->assertEquals(1604, $table[1604]->getId());
        $this->assertTrue($table[1604][0]->isSame('Apparel & Accessories'));
        $this->assertTrue($table[1604][1]->isSame('Clothing'));

        $this->assertEquals(5322, $table[5322]->getId());
        $this->assertTrue($table[5322][0]->isSame('Apparel & Accessories'));
        $this->assertTrue($table[5322][1]->isSame('Clothing'));
        $this->assertTrue($table[5322][2]->isSame('Activewear'));

        $this->assertEquals(5697, $table[5697]->getId());
        $this->assertTrue($table[5697][0]->isSame('Apparel & Accessories'));
        $this->assertTrue($table[5697][1]->isSame('Clothing'));
        $this->assertTrue($table[5697][2]->isSame('Activewear'));
        $this->assertTrue($table[5697][3]->isSame('Bicycle Activewear'));
    }
}
