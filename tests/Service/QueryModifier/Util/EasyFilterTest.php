<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 13:30
 */

namespace Eukles\Service\QueryModifier\Util;

use PHPUnit\Framework\TestCase;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;

class EasyFilterTest extends TestCase
{

    public function testApply()
    {

    }

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

        list($operator, $value) = EasyFilter::build("!%foo,bar");
        $this->assertSame(Criteria::NOT_LIKE, $operator);
        $this->assertSame("%foo,bar", $value);
    }

    public function testCreate()
    {
        $this->assertInstanceOf(EasyFilter::class, EasyFilter::create(new ModelCriteria()));
    }

    public function testIsAutoUseRelationQuery()
    {

    }

    public function testSetAutoUseRelationQuery()
    {

    }
}
