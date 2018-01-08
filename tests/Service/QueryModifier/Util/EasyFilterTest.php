<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 13:30
 */

namespace Eukles\Service\QueryModifier\Util;

use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;

class EasyFilterTest extends TestCase
{

    public function testBuild()
    {
        list($operator, $value) = EasyFilter::build("foo");
        $this->assertSame(null, $operator);
        $this->assertSame("foo", $value);

        list($operator, $value) = EasyFilter::build("!foo");
        $this->assertSame(Criteria::NOT_EQUAL, $operator);
        $this->assertSame("foo", $value);

        list($operator, $value) = EasyFilter::build("foo,bar");
        $this->assertSame(null, $operator);
        $this->assertSame(["foo", "bar"], $value);

        list($operator, $value) = EasyFilter::build("!foo,bar");
        $this->assertSame(Criteria::NOT_IN, $operator);
        $this->assertSame(["foo", "bar"], $value);

        list($operator, $value) = EasyFilter::build("%foo,bar");
        $this->assertSame(Criteria::LIKE, $operator);
        $this->assertSame("%foo,bar", $value);

        list($operator, $value) = EasyFilter::build("foo%");
        $this->assertSame(Criteria::LIKE, $operator);
        $this->assertSame("foo%", $value);

        list($operator, $value) = EasyFilter::build(">1");
        $this->assertSame(Criteria::GREATER_THAN, $operator);
        $this->assertSame("1", $value);

        list($operator, $value) = EasyFilter::build("<1");
        $this->assertSame(Criteria::LESS_THAN, $operator);
        $this->assertSame("1", $value);

        list($operator, $value) = EasyFilter::build(">=1");
        $this->assertSame(Criteria::GREATER_EQUAL, $operator);
        $this->assertSame("1", $value);

        list($operator, $value) = EasyFilter::build("<=1");
        $this->assertSame(Criteria::LESS_EQUAL, $operator);
        $this->assertSame("1", $value);

        list($operator, $value) = EasyFilter::build("!%foo,bar");
        $this->assertSame(Criteria::NOT_LIKE, $operator);
        $this->assertSame("%foo,bar", $value);

        list($operator, $value) = EasyFilter::build("[1,5]");
        $this->assertSame(null, $operator);
        $this->assertSame(["min" => "1", "max" => "5"], $value);
        list($operator, $value) = EasyFilter::build("[1,5");
        $this->assertSame(null, $operator);
        $this->assertSame(["min" => "1", "max" => "5"], $value);

        list($operator, $value) = EasyFilter::build("[1,5]");
        $this->assertSame(null, $operator);
        $this->assertSame(["min" => "1", "max" => "5"], $value);

        list($operator, $value) = EasyFilter::build("[1,5");
        $this->assertSame(null, $operator);
        $this->assertSame(["min" => "1", "max" => "5"], $value);

        list($operator, $value) = EasyFilter::build("[1");
        $this->assertSame(null, $operator);
        $this->assertSame(["min" => "1"], $value);

        list($operator, $value) = EasyFilter::build("[1,");
        $this->assertSame(null, $operator);
        $this->assertSame(["min" => "1"], $value);

        list($operator, $value) = EasyFilter::build("[]");
        $this->assertSame(null, $operator);
        $this->assertSame(null, $value);

        list($operator, $value) = EasyFilter::build("[");
        $this->assertSame(null, $operator);
        $this->assertSame(null, $value);

        list($operator, $value) = EasyFilter::build("'test,quoted'");
        $this->assertSame(null, $operator);
        $this->assertSame("test,quoted", $value);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(EasyFilter::class, EasyFilter::create(new ModelCriteria()));
    }

    public function testApplySimpleFailure()
    {
        $f = new EasyFilter($this->mockQueryInstance());

        # FALSE when relation and no use query
        $this->assertFalse($f->apply('baz', "foo"));
    }

    public function testApplySimpleSuccess()
    {
        $f = new EasyFilter($this->mockQueryInstance());

        # TRUE when no relation
        $this->assertTrue($f->apply('aName', "foo"));
    }

    public function testApplyUseRelationQueryFailures()
    {
        $f = new EasyFilter($this->mockQueryInstance());

        # FALSE when relation and no use query
        $this->assertFalse($f->apply('b.bName', "foo"));

        # FALSE when relation and unknown property
        $f->setAutoUseRelationQuery(true);
        $this->assertFalse($f->apply('b.foo', "foo"));
    }

    public function testApplyUseRelationQuerySuccess()
    {
        $f = new EasyFilter($this->mockQueryInstance());

        # TRUE when no relation and unknown property
        $this->assertTrue($f->apply('aName', "foo"));

        # TRUE when relation and no use query
        $f->setAutoUseRelationQuery(true);
        $this->assertTrue($f->apply('b.bName', "foo"));
        $this->assertTrue($f->apply('b.c.cName', "foo"));
        $this->assertTrue($f->apply('b.c.id', 0));
    }

    public static function setUpBeforeClass()
    {
        if (!class_exists("\\A")) {
            $schema  = file_get_contents(__DIR__ . '/../../../schema-small.xml');
            $builder = new QuickBuilder();
            $builder->setSchema($schema);
            $builder->buildClasses();
        }
    }

    /**
     * @return ModelCriteria $query
     */
    protected function mockQueryInstance()
    {

        /** @var ModelCriteria $query */
        $query = new \AQuery;

        return $query;
    }
}
