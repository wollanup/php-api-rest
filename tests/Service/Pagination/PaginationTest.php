<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 04/04/17
 * Time: 09:17
 */

namespace Eukles\Service\Pagination;

use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    
    public function testCorrect()
    {
        
        $p = new Pagination(2, 3);
        $this->assertEquals(2, $p->getPage());
        $this->assertEquals(3, $p->getLimit());
    }
    
    public function testGetBadLimitShouldReturns1()
    {
        $p = new Pagination(null, 'bob');
        $this->assertEquals(PaginationInterface::DEFAULT_LIMIT, $p->getLimit());
        
        $p = new Pagination(null, '');
        $this->assertEquals(PaginationInterface::DEFAULT_LIMIT, $p->getLimit());
        
        $p = new Pagination(null, false);
        $this->assertEquals(PaginationInterface::DEFAULT_LIMIT, $p->getLimit());
        
        $p = new Pagination(null, ["foo" => "bar"]);
        $this->assertEquals(PaginationInterface::DEFAULT_LIMIT, $p->getLimit());
    }
    
    public function testGetBadePageShouldReturns1()
    {
        $p = new Pagination('bob', null);
        $this->assertEquals(1, $p->getPage());
        
        $p = new Pagination('', null);
        $this->assertEquals(1, $p->getPage());
        
        $p = new Pagination(false, null);
        $this->assertEquals(1, $p->getPage());
        
        $p = new Pagination(["foo" => "bar"], false);
        $this->assertEquals(1, $p->getPage());
    }
    
    public function testGetLimitDefault()
    {
        $p = new Pagination();
        $this->assertEquals(PaginationInterface::DEFAULT_LIMIT, $p->getLimit());
    }
    
    public function testGetPageDefault()
    {
        $p = new Pagination();
        $this->assertEquals(PaginationInterface::DEFAULT_PAGE, $p->getPage());
    }
    
    public function testLimitGtMax()
    {
        $p = new Pagination(null, PaginationInterface::MAX_LIMIT + 10);
        $this->assertEquals(PaginationInterface::MAX_LIMIT, $p->getLimit());
    }
}
