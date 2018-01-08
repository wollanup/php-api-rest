<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 07/07/17
 * Time: 09:59
 */

namespace Test\Eukles\Config;

use Eukles\Config\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{

    public function testExport()
    {
        $configArray = [
            'service' => [
                'key1' => "value1",
                'key2' => "value2",
            ],
        ];

        $config = new Config($configArray);

        $configString   = $config->export();
        $newConfigArray = eval("return " . $configString . ";");

        $this->assertSame($newConfigArray, $configArray);
    }

    public function testIsEnvironment()
    {
        $config = new Config(['app' => ['environment' => "DEV"]]);

        $this->assertTrue($config->isEnvironment('dev'));
        $this->assertTrue($config->isEnvironment('DEV'));
        $this->assertFalse($config->isEnvironment('foo'));
    }

    public function testIsNotProduction()
    {
        $config = new Config(['app' => ['environment' => "DEV"]]);

        $this->assertTrue($config->isNotProduction());
    }
}
