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

use Eukles\Mock\Request;
use Eukles\Service\Request\QueryModifier\RequestQueryModifier;
use Propel\Runtime\ActiveQuery\ModelCriteria;

/**
 * Class RequestQueryModifierTest
 *
 * @package Ged\Service\RequestQueryModifier
 */
class RequestQueryModifierTest extends \PHPUnit_Framework_TestCase
{
    
    public function testApply()
    {
        $rqm = new RequestQueryModifier(new Request());
        $mc  = new ModelCriteria();
        $this->assertEquals($mc, $rqm->apply($mc));
    }
}
