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

use Eukles\Service\QueryModifier\Modifier\Exception\ModifierException;
use Eukles\Service\Request\QueryModifier\Modifier\FilterModifier;
use ModifierTest;
use ModifierTestQuery;
use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\ActiveQuery\Criterion\RawModelCriterion;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Test\Eukles\Request;

/**
 * Class FilterModifierTest
 *
 * @package Ged\Service\RequestQueryModifier
 */
class FilterModifierTest extends TestCase
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

    public function testApplyOnRelation()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "relationTest.name",
                "value"    => "bob",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('.RelationTest.Name = ?', $mc->getMap());
        $criterion = $mc->getMap()['.RelationTest.Name = ?'];
        $this->assertEquals('bob', $criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('RelationTest.Name = ?', $criterion->getColumn());
    }

    public function testApplyWithValue()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "value"    => "test",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertEquals('test', $criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('modifier_test.name = ?', $criterion->getClause());
    }

    public function testApplyWithValueAndInvalidOperator()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "operator" => "invalid",
                "value"    => "test",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $this->expectException(ModifierException::class);
        $m->apply($mc);
    }

    public function testApplyWithValueAndOperator()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "operator" => ">=",
                "value"    => "test",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertEquals('test', $criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('modifier_test.name >= ?', $criterion->getClause());
    }

    public function testApplyWithoutProperty()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "value"    => "test",
                "operator" => "=",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayNotHasKey('modifier_test.name', $mc->getMap());
    }

    public function testApplyWithoutValue()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "operator" => "=",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayNotHasKey('modifier_test.name', $mc->getMap());
    }

    public function testGetName()
    {
        $m = new FilterModifier(new Request);
        $this->assertEquals(FilterModifier::NAME, $m->getName());
    }

    public function testValue()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "value"    => 'foo',
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertEquals('foo', $criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('modifier_test.name = ?', $criterion->getClause());
    }

    public function testValueNullWithEqualsOperator()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "operator" => '=',
                "value"    => null,
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertNull($criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('modifier_test.name  IS NULL', $criterion->getClause());
    }

    public function testValueNullWithNotEqualsOperator()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "operator" => '!=',
                "value"    => null,
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertNull($criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('modifier_test.name  IS NOT NULL', $criterion->getClause());
    }

    public function testValueNullWithoutOperator()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "value"    => null,
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertNull($criterion->getValue());
        $this->assertNull($criterion->getComparison());
        $this->assertEquals('modifier_test.name  IS NULL', $criterion->getClause());
    }
}
