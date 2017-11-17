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

namespace Eukles\Service\RequestQueryModifier;

use Eukles\Service\Request\QueryModifier\RequestQueryModifier;
use Eukles\Test\Util\Request;
use PHPUnit\Framework\TestCase;
use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * Class RequestQueryModifierTest
 *
 * @package Ged\Service\RequestQueryModifier
 */
class RequestQueryModifierTest extends TestCase
{

    public function testApply()
    {
        $rqm = new RequestQueryModifier(new Request);
        $mc  = new ModelCriteria();
        $this->assertEquals($mc, $rqm->apply($mc));
    }

    public function testSetQuery()
    {
        $rqm = new RequestQueryModifier(new Request);
        $rqm->setQuery(new ModelCriteria());
        $mc = new ModelCriteria();
        $this->assertEquals($mc, $rqm->apply($mc));
    }

    public function testWithNonGetMethod()
    {
        $r = new Request;
        $r->setMethod('POST');
        $rqm = new RequestQueryModifier($r);
        $mc  = new ModelCriteria();
        $this->assertEquals($mc, $rqm->apply($mc));
    }
}
