<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/01/18
 * Time: 14:35
 */

namespace Eukles\Service\QueryModifier\Easy;

use AQuery;
use Eukles\Service\QueryModifier\UseQuery\UseQueryFromDotNotationException;
use PHPUnit\Framework\TestCase;
use Propel\Generator\Util\QuickBuilder;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Propel;

class ModifierTest extends TestCase
{

    /**
     *
     */
    public static function setUpBeforeClass()
    {

        if (!class_exists("\\A")) {
            Propel::getServiceContainer()->setAdapterClass("api_behavior_test_0", "sqlite");
            $schema  = file_get_contents(__DIR__ . '/../../../schema-small.xml');
            $builder = new QuickBuilder();
            $builder->setSchema($schema);
            $builder->buildClasses();
        }
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testFilterBy()
    {
        $query    = $this->mockQueryInstance();
        $modifier = new Modifier($query);
        $query    = $modifier->filterBy('AName');
        $this->assertContains("WHERE a.a_name IS NULL", $query->toString());

        $query    = $this->mockQueryInstance();
        $modifier = new Modifier($query);
        $query    = $modifier->filterBy('AName', "foo", Criteria::NOT_EQUAL);
        $this->assertContains("WHERE a.a_name<>:p1", $query->toString());

        $query    = $this->mockQueryInstance();
        $modifier = new Modifier($query);
        $query    = $modifier->filterBy('b.bName');
        $this->assertContains("WHERE _b.b_name", $query->toString());
        $this->assertContains("FROM a INNER JOIN b _b ON (a.id=_b.a_id)", $query->toString());
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testFilterByFailure()
    {
        $query    = $this->mockQueryInstance();
        $modifier = new Modifier($query);
        $modifier->filterBy('Foo');
        $this->assertTrue(in_array('filterByFoo', $modifier->getFailures()));
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    public function testOrderBy()
    {
        $query    = $this->mockQueryInstance();
        $modifier = new Modifier($query);
        $query    = $modifier->orderBy('AName');
        $this->assertContains("ORDER BY a.a_name ASC", $query->toString());

        $query    = $this->mockQueryInstance();
        $modifier = new Modifier($query);
        $query    = $modifier->orderBy('b.bName', 'DESC');
        $this->assertContains("ORDER BY _b.b_name DESC", $query->toString());
    }

    /**
     * @return ModelCriteria $query
     */
    protected function mockQueryInstance()
    {

        /** @var ModelCriteria $query */
        $query = new AQuery;

        return $query;
    }
}
