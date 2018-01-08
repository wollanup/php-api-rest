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

class EasySortTest extends TestCase
{

    public static function setUpBeforeClass()
    {
        if (!class_exists("\\A")) {
            $schema  = file_get_contents(__DIR__ . '/../../../schema-small.xml');
            $builder = new QuickBuilder();
            $builder->setSchema($schema);
            $builder->buildClasses();
        }
    }

    public function testApplySimpleFailure()
    {
        $f = new EasySort($this->mockQueryInstance());

        # FALSE when relation and no use query
        $this->assertFalse($f->apply('baz'));
    }

    public function testApplySimpleSuccess()
    {
        $f = new EasySort($this->mockQueryInstance());

        # TRUE when relation and no use query
        $this->assertTrue($f->apply('-aName'));
    }

    public function testApplyUseRelationQueryFailures()
    {
        $f = new EasySort($this->mockQueryInstance());

        # FALSE when relation and no use query
        $this->assertFalse($f->apply('-b.bName'));

        # FALSE when relation and unknown property
        $f->setAutoUseRelationQuery(true);
        $this->assertFalse($f->apply('b.foo'));

        # FALSE when relation and bad placement of direction
        $f->setAutoUseRelationQuery(true);
        $this->assertFalse($f->apply('b.-foo'));

        # FALSE when bad relation
        $f->setAutoUseRelationQuery(true);
        $this->assertFalse($f->apply('bad.foo'));
    }

    public function testApplyUseRelationQuerySuccess()
    {
        $f = new EasySort($this->mockQueryInstance());
        $f->setAutoUseRelationQuery(true);

        $this->assertTrue($f->apply('-aName'));
        $this->assertTrue($f->apply('-b.bName'));
        $this->assertTrue($f->apply('-b.c.cName'));
        $this->assertTrue($f->apply('-b.c.id'));

        $this->assertSame([
            0 => 'a.a_name DESC',
            1 => 'b.b_name DESC',
            2 => 'c.c_name DESC',
            3 => 'c.id DESC',
        ], $f->getQuery()->getOrderByColumns());
    }

    public function testBuild()
    {
        list($property, $direction) = EasySort::build("foo");
        $this->assertSame("foo", $property);
        $this->assertSame($direction, Criteria::ASC);

        list($property, $direction) = EasySort::build("+foo");
        $this->assertSame("foo", $property);
        $this->assertSame($direction, Criteria::ASC);

        list($property, $direction) = EasySort::build("-foo");
        $this->assertSame("foo", $property);
        $this->assertSame($direction, Criteria::DESC);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(EasySort::class, EasySort::create(new ModelCriteria()));
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
