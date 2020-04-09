<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/03/17
 * Time: 11:52
 */

namespace Eukles\Util;

use Eukles\Util\ClassName\ClassName;
use Eukles\Util\ClassName\FileNotFoundException;
use Eukles\Util\ClassName\InvalidClassException;
use PHPUnit\Framework\TestCase;

class ClassNameTest extends TestCase
{

    public function testGetFromFileWhichIsAClass()
    {
        $this->assertEquals('\\' . self::class, ClassName::getFromFile(__FILE__));
    }

    public function testGetFromFileWhichIsNotClass()
    {
        $file = sys_get_temp_dir() . '/phpunitTestFile';
        touch($file);
        $this->expectException(InvalidClassException::class);
        ClassName::getFromFile($file);
        unlink($file);
    }

    public function testGetFromFileWhichNotExist()
    {
        $this->expectException(FileNotFoundException::class);
        ClassName::getFromFile("this/file.shouldNotExists");
    }
}
