<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 13:30
 */

namespace Eukles\Service\QueryModifier\UseQuery;

use Eukles\Service\QueryModifier\Easy\Builder\Sort;
use PHPUnit\Framework\TestCase;
use Propel\Runtime\ActiveQuery\Criteria;

class EasySortTest extends TestCase
{

    public function testBuild()
    {
        $builder = new Sort("foo");
        $this->assertSame("foo", $builder->getProperty());
        $this->assertSame($builder->getDirection(), Criteria::ASC);

        $builder = new Sort("+foo");
        $this->assertSame("foo", $builder->getProperty());
        $this->assertSame($builder->getDirection(), Criteria::ASC);

        $builder = new Sort("-foo");
        $this->assertSame("foo", $builder->getProperty());
        $this->assertSame($builder->getDirection(), Criteria::DESC);
    }

    public function testBuildEmpty()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Sort("");
    }
}
