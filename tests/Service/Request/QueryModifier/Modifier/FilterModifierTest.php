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
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Criterion\RawModelCriterion;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Adapter\Pdo\MysqlAdapter;
use Propel\Runtime\Propel;
use Test\Eukles\Request;

/**
 * Class FilterModifierTest
 *
 * @package Ged\Service\RequestQueryModifier
 */
class FilterModifierTest extends TestCase
{

    /**
     * Just to store the result of createSelectSql
     * @var array
     */
    private $arr = [];

    public function setUp()
    {
        if (!class_exists(ModifierTest::class)) {

            $b = new QuickBuilder;
            $b->setSchema('
<database name="modifier_test_db">
	<table name="modifier_test">
		<column name="name" type="VARCHAR"/>
		<column name="column2" type="VARCHAR"/>
		<column name="date" type="TIMESTAMP"/>
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
            Propel::getServiceContainer()->setAdapter('modifier_test_db', new MysqlAdapter());
        }
    }

    public function testApplyOnRelation()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "relationTest.name",
                "value"    => "bob",
                "operator"    => "<>",
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('_relationTest.name', $mc->getMap());
        $criterion = $mc->getMap()['_relationTest.name'];
        $this->assertEquals('bob', $criterion->getValue());
        $this->assertEquals(Criteria::NOT_EQUAL, $criterion->getComparison());
        $this->assertEquals('_relationTest', $criterion->getTable());
        $this->assertEquals('name', $criterion->getColumn());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test INNER JOIN relation_test _relationTest ON (modifier_test.relation_id=_relationTest.id) WHERE _relationTest.name<>:p1");
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
        $this->assertEquals('=', $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name=:p1");
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
        $this->assertEquals('>=', $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name>=:p1");
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

    public function testInArray()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "value"    => [
                    "toto", "tata"
                ],
                "operator"    => 'IN',
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertEquals('name', $criterion->getColumn());
        $this->assertEquals(["toto", "tata"], $criterion->getValue());
        $this->assertEquals(Criteria::IN, $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name IN (:p1,:p2)");
    }

    public function testMinMax()
    {
        $m = new FilterModifier(new Request([
        "filter" => json_encode([
                "property" => "date",
                "value"    => [
                    "min" => "01-10-2020",
                    "max" => "20-10-2020"
                ],
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.date', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.date'];
        $this->assertEquals('date', $criterion->getColumn());
        $this->assertEquals("01-10-2020", $criterion->getValue());
        $this->assertEquals(Criteria::GREATER_EQUAL, $criterion->getComparison());

        $this->assertEquals(1,  count($criterion->getClauses()));
        $this->assertEquals("20-10-2020", $criterion->getClauses()[0]->getValue());
        $this->assertEquals("date", $criterion->getClauses()[0]->getColumn());
        $this->assertEquals(Criteria::LESS_EQUAL, $criterion->getClauses()[0]->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE (modifier_test.date>=:p1 AND modifier_test.date<=:p2)");
    }

    public function testLike()
    {
        $m = new FilterModifier(new Request([
            "filter" => json_encode([
                "property" => "name",
                "value"    => '%foo%',
                "operator"    => 'LIKE',
            ]),
        ]));
        /** @var ModelCriteria $mc */
        $mc = new ModifierTestQuery();
        $m->apply($mc);
        $this->assertArrayHasKey('modifier_test.name', $mc->getMap());
        /** @var RawModelCriterion $criterion */
        $criterion = $mc->getMap()['modifier_test.name'];
        $this->assertEquals('name', $criterion->getColumn());
        $this->assertEquals('%foo%', $criterion->getValue());
        $this->assertEquals(Criteria::LIKE, $criterion->getComparison());

        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name LIKE :p1");
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
        $this->assertEquals('name', $criterion->getColumn());
        $this->assertEquals('foo', $criterion->getValue());
        $this->assertEquals('=', $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name=:p1");
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
        $this->assertEquals(Criteria::ISNULL, $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name IS NULL ");
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
        $this->assertEquals(Criteria::ISNOTNULL, $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr),"SELECT  FROM modifier_test WHERE modifier_test.name IS NOT NULL ");
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
        $this->assertEquals(Criteria::ISNULL, $criterion->getComparison());
        $this->assertEquals($mc->createSelectSql($this->arr), "SELECT  FROM modifier_test WHERE modifier_test.name IS NULL ");
    }
}
