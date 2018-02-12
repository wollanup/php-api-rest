<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 13:30
 */

namespace Eukles\Service\QueryModifier\UseQuery;

use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\Exception\RelationNotFoundException;

class UseQueryFromDotNotationTest extends TestCase
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

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testEndUse()
    {

        $query = $this->mockQueryInstance();
        $use   = new UseQueryFromDotNotation($query, "aName");
        $use->useQuery();
        $this->assertSame(0, $use->getDepth());
        $usedQuery = $use->endUse();
        $this->assertInstanceOf(\AQuery::class, $usedQuery);
        $this->assertSame("AName", $use->getProperty());
        $this->assertFalse($use->isInUse());

        $use = new UseQueryFromDotNotation($query, "b.bName");
        $use->useQuery();
        $this->assertSame(1, $use->getDepth());
        $usedQuery = $use->endUse();
        $this->assertInstanceOf(\AQuery::class, $usedQuery);
        $this->assertSame("BName", $use->getProperty());
        $this->assertFalse($use->isInUse());

        $use = new UseQueryFromDotNotation($query, "b.c.cName");
        $use->useQuery();
        $this->assertSame(2, $use->getDepth());
        $usedQuery = $use->endUse();
        $this->assertInstanceOf(\AQuery::class, $usedQuery);
        $this->assertSame("CName", $use->getProperty());
        $this->assertFalse($use->isInUse());
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testEndUseBeforeUse()
    {
        $query = $this->mockQueryInstance();
        $use   = new UseQueryFromDotNotation($query, "zzz.foo");
        $this->expectException(UseQueryFromDotNotationException::class);
        $use->endUse();
    }

    /**
     */
    public function testSeparatorValue()
    {

        $this->assertSame(".", UseQueryFromDotNotation::RELATION_SEP);
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testUseQuery()
    {
        $query     = $this->mockQueryInstance();
        $use       = new UseQueryFromDotNotation($query, "foo");
        $usedQuery = $use->useQuery();
        $this->assertInstanceOf(\AQuery::class, $usedQuery);
        $this->assertTrue(method_exists($usedQuery, "filterByAName"));

        $query     = $this->mockQueryInstance();
        $use       = new UseQueryFromDotNotation($query, "b.foo");
        $usedQuery = $use->useQuery();
        $this->assertInstanceOf(\BQuery::class, $usedQuery);
        $this->assertTrue(method_exists($usedQuery, "filterByBName"));

        $query     = $this->mockQueryInstance();
        $use       = new UseQueryFromDotNotation($query, "b.c.foo");
        $usedQuery = $use->useQuery();
        $this->assertInstanceOf(\CQuery::class, $usedQuery);
        $this->assertTrue(method_exists($usedQuery, "filterByCName"));
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testUseQueryAlreadyInUse()
    {
        $query = $this->mockQueryInstance();
        $use   = new UseQueryFromDotNotation($query, "b.foo");
        $use->useQuery();
        $this->expectException(UseQueryFromDotNotationException::class);
        $use->useQuery();
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testUseQueryRelationNotFound()
    {
        $query = $this->mockQueryInstance();
        $use   = new UseQueryFromDotNotation($query, "zzz.foo");
        $this->expectException(RelationNotFoundException::class);
        $use->useQuery();
    }

    public function testUseQueryWithBadProperty()
    {
        $query = $this->mockQueryInstance();
        $this->expectException(\InvalidArgumentException::class);
        new UseQueryFromDotNotation($query, "b.");
    }

    public function testUseQueryWithDotBeforeProperty()
    {
        $query = $this->mockQueryInstance();
        $use   = new UseQueryFromDotNotation($query, ".b");
        $this->assertSame(0, $use->getDepth());
    }

    public function testUseQueryWithEmptyProperty()
    {
        $query = $this->mockQueryInstance();
        $this->expectException(\InvalidArgumentException::class);
        new UseQueryFromDotNotation($query, "");
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
