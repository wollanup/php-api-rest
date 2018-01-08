<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 13:30
 */

namespace Eukles\Service\QueryModifier\Util;

use PHPUnit\Framework\TestCase;
use Propel\Runtime\ActiveQuery\ModelCriteria;

class EasyUtilTest extends TestCase
{

    public function testGetQuery()
    {
        $query = new ModelCriteria;
        /** @var EasyUtil $t */
        $t = $this->getMockForAbstractClass(EasyUtil::class, [$query]);
        $this->assertSame($query, $t->getQuery());
    }

    public function testIsAutoUseRelationQuery()
    {
        /** @var EasyUtil $t */
        $t = $this->getMockForAbstractClass(EasyUtil::class, [new ModelCriteria]);
        $this->assertFalse($t->isAutoUseRelationQuery());
    }

    public function testSetAutoUseRelationQuery()
    {
        /** @var EasyUtil $t */
        $t = $this->getMockForAbstractClass(EasyUtil::class, [new ModelCriteria]);
        $t->setAutoUseRelationQuery(true);
        $this->assertTrue($t->isAutoUseRelationQuery());
    }
}
