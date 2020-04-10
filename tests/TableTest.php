<?php
namespace Tests;

use GoogleTaxonomyHandler\Builder;
use PHPUnit\Framework\TestCase;

class TableTest extends TestCase
{
    public function testMatch()
    {
        $builder = new Builder();
        $table = $builder->loadFromFile('taxonomy-with-ids.en-US-20190710.txt')->buildTable();

        $id = $table->match('Sporting Goods, Baseball Batting Helmets', ', ');
        $this->assertEquals(3668, $id);

        $id = $table->match('Apparel & Accessories, Clothing, Pants', ', ');
        $this->assertEquals(204, $id);

        $id = $table->match('Apparel & Accessories , Bottoms , Jeans', ', ');
        $this->assertEquals(166, $id);

        $id = $table->match('Apparel & Accessories , Shirts & Tops , Sweaters', ', ');
        $this->assertEquals(212, $id);
    }
}
