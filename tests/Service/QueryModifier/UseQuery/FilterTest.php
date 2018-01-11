<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 13:30
 */

namespace Eukles\Service\QueryModifier\UseQuery;

use Eukles\Service\QueryModifier\Easy\Builder\Filter;
use PHPUnit\Framework\TestCase;
use Propel\Runtime\ActiveQuery\Criteria;

class FilterTest extends TestCase
{

    public function testBrackets()
    {
        $builder = new Filter("[1,5]");
        $this->assertSame(Filter::BETWEEN, $builder->getOperator());
        $this->assertSame(["min" => "1", "max" => "5"], $builder->getValue());

        $builder = new Filter("[1,5");
        $this->assertSame(Filter::BETWEEN, $builder->getOperator());
        $this->assertSame(["min" => "1", "max" => "5"], $builder->getValue());

        $builder = new Filter("[1,5]");
        $this->assertSame(Filter::BETWEEN, $builder->getOperator());
        $this->assertSame(["min" => "1", "max" => "5"], $builder->getValue());

        $builder = new Filter("[1,5");
        $this->assertSame(Filter::BETWEEN, $builder->getOperator());
        $this->assertSame(["min" => "1", "max" => "5"], $builder->getValue());

        $builder = new Filter("[1");
        $this->assertSame(Criteria::GREATER_EQUAL, $builder->getOperator());
        $this->assertSame("1", $builder->getValue());

        $builder = new Filter("[1,");
        $this->assertSame(Criteria::GREATER_EQUAL, $builder->getOperator());
        $this->assertSame("1", $builder->getValue());

        $builder = new Filter("[]");
        $this->assertSame(Criteria::ISNULL, $builder->getOperator());
        $this->assertSame(null, $builder->getValue());

        $builder = new Filter("[");
        $this->assertSame(Criteria::ISNULL, $builder->getOperator());
        $this->assertSame(null, $builder->getValue());
    }

    public function testEqual()
    {
        $builder = new Filter("foo");
        $this->assertSame(Criteria::EQUAL, $builder->getOperator());
        $this->assertSame("foo", $builder->getValue());

        $builder = new Filter("!foo");
        $this->assertSame(Criteria::NOT_EQUAL, $builder->getOperator());
        $this->assertSame("foo", $builder->getValue());
    }

    public function testGreaterAndLess()
    {
        $builder = new Filter(">1");
        $this->assertSame(Criteria::GREATER_THAN, $builder->getOperator());
        $this->assertSame("1", $builder->getValue());

        $builder = new Filter("<1");
        $this->assertSame(Criteria::LESS_THAN, $builder->getOperator());
        $this->assertSame("1", $builder->getValue());

        $builder = new Filter(">=1");
        $this->assertSame(Criteria::GREATER_EQUAL, $builder->getOperator());
        $this->assertSame("1", $builder->getValue());

        $builder = new Filter("<=1");
        $this->assertSame(Criteria::LESS_EQUAL, $builder->getOperator());
        $this->assertSame("1", $builder->getValue());
    }

    public function testIn()
    {
        $builder = new Filter("foo,bar");
        $this->assertSame(Criteria::IN, $builder->getOperator());
        $this->assertSame(["foo", "bar"], $builder->getValue());

        $builder = new Filter("!foo,bar");
        $this->assertSame(Criteria::NOT_IN, $builder->getOperator());
        $this->assertSame(["foo", "bar"], $builder->getValue());
    }

    public function testLike()
    {
        $builder = new Filter("%foo,bar");
        $this->assertSame(Criteria::LIKE, $builder->getOperator());
        $this->assertSame("%foo,bar", $builder->getValue());

        $builder = new Filter("foo%");
        $this->assertSame(Criteria::LIKE, $builder->getOperator());
        $this->assertSame("foo%", $builder->getValue());

        $builder = new Filter("!%foo%");
        $this->assertSame(Criteria::NOT_LIKE, $builder->getOperator());
        $this->assertSame("%foo%", $builder->getValue());
    }

    public function testNull()
    {
        $builder = new Filter("null");
        $this->assertSame(Criteria::ISNULL, $builder->getOperator());
        $this->assertSame(null, $builder->getValue());

        $builder = new Filter("!null");
        $this->assertSame(Criteria::ISNOTNULL, $builder->getOperator());
        $this->assertSame(null, $builder->getValue());

        $builder = new Filter("!foonull");
        $this->assertSame(Criteria::NOT_EQUAL, $builder->getOperator());
        $this->assertSame("foonull", $builder->getValue());
    }

    public function testQuoted()
    {
        $builder = new Filter("'test,quoted'");
        $this->assertSame(Criteria::EQUAL, $builder->getOperator());
        $this->assertSame("test,quoted", $builder->getValue());
        $builder = new Filter("'null'");
        $this->assertSame(Criteria::EQUAL, $builder->getOperator());
        $this->assertSame("null", $builder->getValue());
        $builder = new Filter('!"null"');
        $this->assertSame(Criteria::NOT_EQUAL, $builder->getOperator());
        $this->assertSame("null", $builder->getValue());
    }
}
