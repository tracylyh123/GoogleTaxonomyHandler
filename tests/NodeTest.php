<?php
namespace Tests;

use GoogleTaxonomyHandler\Node;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    public function testResolve()
    {
        $node = new Node('Animals & Pet Supplies');
        $this->assertEquals(['Animals Supplies', 'Pet Supplies'], $node->resolve());

        $node = new Node('Apparel & Accessories');
        $this->assertEquals(['Apparel', 'Accessories'], $node->resolve());

        $node = new Node('Garden Arches, Trellises, Arbors & Pergolas');
        $this->assertEquals(['Garden Arches', 'Trellises', 'Arbors', 'Pergolas'], $node->resolve());

        $node = new Node('Baseball & Softball Gloves & Mitts');
        $this->assertEquals(['Baseball Gloves', 'Baseball Mitts', 'Softball Gloves', 'Softball Mitts'], $node->resolve());

        $node = new Node('Baseball & Softball Batting Helmets');
        $this->assertEquals(['Baseball Batting Helmets', 'Softball Batting Helmets'], $node->resolve());
    }
}
