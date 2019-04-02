<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 03/04/17
 * Time: 11:02
 */

namespace Service\Request\Pagination;

use Eukles\Service\Pagination\PaginationInterface;
use Eukles\Service\Request\Pagination\RequestPagination;
use Eukles\Test\Util\Request;
use PHPUnit\Framework\TestCase;

class RequestPaginationTest extends TestCase
{

    public function testCorrect()
    {
        $r = new Request([
            'limit' => 3,
            'page'  => 2,
        ]);

        $rp = new RequestPagination($r);
        $this->assertEquals(2, $rp->getPage());
        $this->assertEquals(3, $rp->getLimit());
    }

    public function testGetBadLimitShouldReturns1()
    {
        $r  = new Request(['limit' => 'bob',]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getLimit());

        $r  = new Request(['limit' => '',]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getLimit());

        $r  = new Request(['limit' => false,]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getLimit());

        $r  = new Request(['limit' => ["foo" => "bar"],]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getLimit());
    }

    public function testGetBadePageShouldReturns1()
    {
        $r  = new Request(['page' => 'bob',]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getPage());

        $r  = new Request(['page' => '',]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getPage());

        $r  = new Request(['page' => false,]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getPage());

        $r  = new Request(['page' => ["foo" => "bar"],]);
        $rp = new RequestPagination($r);
        $this->assertEquals(1, $rp->getPage());
    }

    public function testGetLimitDefault()
    {
        $rp = new RequestPagination(new Request());
        $this->assertEquals(PaginationInterface::DEFAULT_LIMIT, $rp->getLimit());
    }

    public function testGetPageDefault()
    {
        $rp = new RequestPagination(new Request());
        $this->assertEquals(PaginationInterface::DEFAULT_PAGE, $rp->getPage());
    }

    public function testLimitGtMax()
    {
        $rp = new RequestPagination(new Request(["limit" => PaginationInterface::MAX_LIMIT + 10]));
        $this->assertEquals(PaginationInterface::MAX_LIMIT, $rp->getLimit());
    }

    public function testWithNonGetMethod()
    {
        $r = new Request([
            'limit' => 3,
            'page'  => 2,
        ]);
        $r->setMethod('POST');
        $rp = new RequestPagination($r);
        $this->assertSame(2, $rp->getPage());
        $this->assertsame(3, $rp->getLimit());
    }
}
