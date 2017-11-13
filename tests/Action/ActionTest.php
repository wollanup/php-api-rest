<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 29/07/16
 * Time: 11:25
 */

namespace Test\Eukles\Action;

use Eukles\Action\ActionAbstract;
use Eukles\Action\ActionInterface;
use PHPUnit\Framework\TestCase;
use Test\Eukles\Mock\Container;
use Test\Eukles\Mock\Request;
use Test\Eukles\Mock\Response;

class ActionTest extends TestCase
{

    public function testConstruct()
    {
        $a = $this->getMockForAbstractClass(ActionAbstract::class, [], "", false);
        $this->assertTrue($a instanceof ActionInterface);
    }

    public function testConstructWithContainer()
    {
        $c = new Container();
        $a = $this->getMockForAbstractClass(ActionAbstract::class, [$c]);
        $this->assertTrue($a instanceof ActionInterface);
    }

    public function testRequest()
    {
        /** @var ActionInterface $a */
        $a = $this->getMockForAbstractClass(ActionAbstract::class, [], "", false);
        $r = new Request();
        $a->setRequest($r);
        $this->assertEquals($r, $a->getRequest());
    }

    public function testResponse()
    {
        /** @var ActionInterface $a */
        $a = $this->getMockForAbstractClass(ActionAbstract::class, [], "", false);
        $r = new Response();
        $a->setResponse($r);
        $this->assertEquals($r, $a->getResponse());
    }
}
