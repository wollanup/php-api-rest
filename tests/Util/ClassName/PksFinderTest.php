<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 21/04/17
 * Time: 10:10
 */

namespace Eukles\Util;

use PHPUnit\Framework\TestCase;
use stdClass;

class PksFinderTest extends TestCase
{

    public function testArrayOfArray()
    {
        $finder = new PksFinder();
        $data = [
            ['id' => 123],
            ['id' => 456],
        ];
        $pks = $finder->find($data);
        $this->assertSame([123, 456], $pks);
    }

    public function testArrayOfArrayWithCustomPk()
    {
        $finder = new PksFinder(["ID"]);
        $data = [
            ['ID' => 123],
            ['ID' => 456],
        ];
        $pks = $finder->find($data);
        $this->assertSame([123, 456], $pks);
    }

    public function testArrayOfArrayWithoutPkKey()
    {
        $finder = new PksFinder();
        $data = [
            ['foo' => 123],
            ['bar' => 456],
        ];
        $pks = $finder->find($data);
        $this->assertSame([], $pks);
    }

    public function testArrayOfObjectsIsConvertedToArray()
    {
        $finder = new PksFinder();

        $data1 = new stdClass();
        $data1->id = 123;
        $data2 = new stdClass();
        $data2->id = 456;
        $data = [
            $data1,
            $data2,
        ];
        $pks = $finder->find($data);
        $this->assertSame([123, 456], $pks);
    }

    public function testCaseMattersInArrayKeys()
    {
        $finder = new PksFinder();
        $data = [
            ['PK' => 123],
            ['ID' => 456],
        ];
        $pks = $finder->find($data);
        $this->assertSame([], $pks);
    }

    public function testEmptyArrayShouldReturnsItself()
    {
        $finder = new PksFinder();
        $data = [];
        $pks = $finder->find($data);
        $this->assertSame($data, $pks);
    }

    public function testSimpleArrayShouldReturnsItself()
    {
        $finder = new PksFinder();
        $data = [1, 123, 'bob'];
        $pks = $finder->find($data);
        $this->assertSame($data, $pks);
    }
}
