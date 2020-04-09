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

namespace Eukles\Service\RequestQueryModifier\Base;

use Eukles\Service\Request\QueryModifier\Modifier\Base\ModifierBase;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use PHPUnit_Framework_MockObject_InvocationMocker;
use Test\Eukles\Request;

/**
 * Class ModifierTestBase
 *
 * @package Ged\Service\RequestQueryModifier
 */
class ModifierBaseTest extends TestCase
{

    /**
     * @var ModifierBase|PHPUnit_Framework_MockObject_InvocationMocker $modifier
     */
    protected $modifier = null;

    public function setUp()
    {
        /** @var ModifierBase|PHPUnit_Framework_MockObject_InvocationMocker $modifier */
        $this->modifier = $this->getMockForAbstractClass(ModifierBase::class, [new Request()]);

        $this->modifier->expects($this->any())
            ->method('getName')
            ->will($this->returnValue('test'));
    }

    public function testAutoListIfSingleFilterWithKeyProperty()
    {
        $r = new Request(["test" => json_encode(["property" => "name"])]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals([["property" => "name"]], $this->modifier->getModifiers());
    }

    public function testCorrectFilter()
    {
        $r = new Request(["test" => json_encode([["filter"]])]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals([["filter"]], $this->modifier->getModifiers());
    }

    public function testGetModifier()
    {
        $r = new Request(["test" => json_encode(["property" => "name"])]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals(["property" => "name"], $this->modifier->getModifier('name'));
        $this->expectException(InvalidArgumentException::class);
        $this->modifier->getModifier('otherName');
    }

    public function testHasModifier()
    {
        $r = new Request(["test" => json_encode(["property" => "name"])]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertTrue($this->modifier->hasModifier('name'));
        $this->assertFalse($this->modifier->hasModifier('otherName'));
    }

    public function testInvalidModifiers()
    {
        $r = new Request(["notest" => 'test']);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals([], $this->modifier->getModifiers());

        $r = new Request(["test" => "value"]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals([], $this->modifier->getModifiers());

        $r = new Request(["test" => false]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals([], $this->modifier->getModifiers());

        $r = new Request(["test" => 123]);
        $this->modifier->setModifierFromRequest($r);
        $this->assertEquals([], $this->modifier->getModifiers());
    }

    public function testRemoveModifier()
    {
        $r = new Request(["test" => json_encode(["property" => "name"])]);
        $this->modifier->setModifierFromRequest($r);
        $this->modifier->removeModifier('name');
        $this->assertFalse($this->modifier->hasModifier('name'));
    }

//    use MockEnvironment;
//
//    /** @var string */
//    protected $documentCreateDatePhpName;
//    /** @var string */
//    protected $documentFileNamePhpName;
//    /** @var string */
//    protected $documentNamePhpName;
//
//    /**
//     * Set up some useful class property for test readability purpose
//     */
//    public function setUp()
//    {
//        $this->documentNamePhpName       = DocumentTableMap::getTableMap()->getColumn(DocumentTableMap::COL_DOCUMENT_LIBELLE)->getPhpName();
//        $this->documentCreateDatePhpName = DocumentTableMap::getTableMap()->getColumn(DocumentTableMap::COL_DOCUMENT_DATE_CREATION)->getPhpName();
//        $this->documentFileNamePhpName   = sprintf('%s.%s',
//            FileTableMap::getTableMap()->getPhpName(),
//            FileTableMap::getTableMap()->getColumn(FileTableMap::COL_FICHIER_NOM)->getPhpName()
//        );
//    }
//
//    /**
//     * Test that if only one modifier is sent in request, the modifier works like if there are few ones
//     */
//    public function testWithOneModifierInRequest()
//    {
//        # We get the Modifier class
//        $modifierClass = $this->getTestedModifierClassFromCalledClass();
//
//        $container = $this->mockEnvironment([
//            $modifierClass::NAME => json_encode(['property' => $this->documentNamePhpName])
//        ]);
//        $query     = (new DocumentAction($container))->createQuery();
//        /** @var ModifierBase $modifier */
//        $modifier = new $modifierClass($container->getRequest());
//        $modifier($query);
//
//        $this->assertNotEmpty($modifier->getModifiers());
//    }
//
//    /**
//     * Test that if an empty modifier is sent in the request, the SortModifier should contain an empty modifiers property
//     */
//    public function testWithSetButEmptyModifierParamInRequest()
//    {
//        # We get the Modifier class
//        $modifierClass = $this->getTestedModifierClassFromCalledClass();
//
//        $container = $this->mockEnvironment([
//            $modifierClass::NAME => json_encode([])
//        ]);
//        $query     = (new DocumentAction($container))->createQuery();
//        /** @var ModifierBase $modifier */
//        $modifier = new $modifierClass($container->getRequest());
//        $modifier($query);
//
//        $this->assertTrue(is_array($modifier->getModifiers()));
//        $this->assertEmpty($modifier->getModifiers());
//    }
//
//    /**
//     * Return the Class on which the test are run
//     *
//     * @return ModifierBase
//     */
//    protected function getTestedModifierClassFromCalledClass()
//    {
//        # We get the Modifier class
//        $reflection = New \ReflectionClass(get_called_class());
//        return sprintf('%s\%s', $reflection->getNamespaceName(), str_ireplace('Test', '', $reflection->getShortName()));
//    }
//
//
//    /**
//     * List of tables needed to run your tests
//     *
//     * @return array
//     */
//    public static function requiredTables()
//    {
//        return [
//            DocumentTableMap::class
//        ];
//    }
}
