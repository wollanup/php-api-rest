<?php
/**
 * File description
 *
 * @package
 * @version      $LastChangedRevision:$
 *               $LastChangedDate:$
 * @link         $HeadURL:$
 * @author       $LastChangedBy:$
 */

namespace Eukles\Service\RequestQueryModifier\Base;

use Eukles\Service\Request\QueryModifier\Modifier\SortModifier;
use ModifierTest;
use ModifierTestQuery;
use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;
use Test\Eukles\Request;

/**
 * Class SortModifierTest
 *
 * @package Ged\Service\RequestQueryModifier
 */
class SortModifierTest extends TestCase
{

    public function setUp()
    {
        if (!class_exists(ModifierTest::class)) {

            $b = new QuickBuilder;
            $b->setSchema('
<database name="modifier_test_db">
	<table name="modifier_test">
		<column name="name" type="VARCHAR"/>
		<column name="column2" type="VARCHAR"/>
		<column name="relation_id" type="INTEGER"/>
		<foreign-key foreignTable="relation_test">
			<reference local="relation_id" foreign="id"/>
		</foreign-key>
	</table>
	
	<table name="relation_test">
		<behavior name="autoAddPk"/>
		<column name="name" type="VARCHAR"/>
		<column name="column2" type="VARCHAR"/>
	</table>
</database>
');
            $b->buildClasses();
        }
    }

    public function testApplyAsc()
    {
        $m = new SortModifier(new Request(["sort" => json_encode(["property" => "name", "direction" => "asc"])]));
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertEquals('modifier_test.name ASC', $mc->getOrderByColumns()[0]);
    }

    public function testApplyDesc()
    {
        $m = new SortModifier(new Request(["sort" => json_encode(["property" => "name", "direction" => "desc"])]));
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertEquals('modifier_test.name DESC', $mc->getOrderByColumns()[0]);
    }

    public function testApplyMulti()
    {
        $m = new SortModifier(new Request([
            "sort" => json_encode([
                    ["property" => "name", "direction" => "asc"],
                    ["property" => "column2", "direction" => "asc"],
                ]
            ),
        ]));
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertEquals('modifier_test.name ASC', $mc->getOrderByColumns()[0]);
        $this->assertEquals('modifier_test.column2 ASC', $mc->getOrderByColumns()[1]);
    }

    public function testSetModifierFromRequest()
    {
        $r = new Request(["sort" => ["property" => "name"], "foo" => "bar"]);
        $m = new SortModifier($r);
        $m->setModifierFromRequest($r);
        $this->assertSame(["property" => "name"], $m->getModifier('name'));

        $r = new Request(["sort" => "-name", "foo" => "bar"]);
        $m = new SortModifier($r);
        $m->setModifierFromRequest($r);
        $this->assertSame(["property" => "name", "direction" => "DESC"], $m->getModifier('name'));

        $r = new Request(["sort" => "-name,foo"]);
        $m = new SortModifier($r);
        $m->setModifierFromRequest($r);
        $this->assertSame(["property" => "name", "direction" => "DESC"], $m->getModifier('name'));
        $this->assertSame(["property" => "foo", "direction" => "ASC"], $m->getModifier('foo'));
    }

    public function testApplyOnInexistentField()
    {
        $m = new SortModifier(new Request(["sort" => json_encode(["property" => "notFound"])]));
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertEquals([], $mc->getOrderByColumns());
    }

    public function testApplyWithoutDirectionIsDesc()
    {
        $m = new SortModifier(new Request(["sort" => json_encode(["property" => "name"])]));
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertEquals('modifier_test.name DESC', $mc->getOrderByColumns()[0]);
    }

    public function testGetName()
    {
        $m = new SortModifier(new Request);
        $this->assertEquals(SortModifier::NAME, $m->getName());
    }

    public function testInexistentRelation()
    {
        $m = new SortModifier(new Request([
            "sort" => json_encode([
                "property"  => "RelationNotFound.Name",
                "direction" => "asc",
            ]),
        ]));
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertEquals([], $mc->getOrderByColumns());
    }

    public function testRelation()
    {
        $m = new SortModifier(new Request([
            "sort" => json_encode([
                "property"  => "RelationTest.Name",
                "direction" => "asc",
            ]),
        ]));
        $mc = new ModifierTestQuery();
        $mc->joinWithRelationTest();
        $m->apply($mc);
        $this->assertEquals('relation_test.name ASC', $mc->getOrderByColumns()[0]);
    }

    public function testRelationWithSlash()
    {
        $m = new SortModifier(new Request([
            "sort" => json_encode([
                "property"  => "RelationTest/Name",
                "direction" => "asc",
            ]),
        ]));
        $mc = new ModifierTestQuery();
        $mc->joinWithRelationTest();
        $m->apply($mc);
        $this->assertEquals('relation_test.name ASC', $mc->getOrderByColumns()[0]);
    }
}
