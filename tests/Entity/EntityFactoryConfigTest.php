<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 29/07/16
 * Time: 11:25
 */

namespace Test\Eukles\Action;

use Eukles\Entity\EntityFactoryConfig;
use Eukles\Entity\EntityFactoryConfigException;
use Eukles\Entity\EntityRequestAbstract;
use PHPUnit\Framework\TestCase;
use Slim\Http\Environment;
use Test\Eukles\Mock\Container;
use Test\Eukles\Mock\Request;

class EntityFactoryConfigTest extends TestCase
{

    public function testSetGetEntityRequest()
    {
        $efc = new EntityFactoryConfig();
        $this->assertFalse($efc->issetEntityRequest());
        $efc->setEntityRequest(Request::class);
        $this->assertTrue($efc->issetEntityRequest());
        $this->assertSame(Request::class, $efc->getEntityRequest());
    }

    public function testSetGetHydrateEntityFromRequest()
    {
        $efc = new EntityFactoryConfig();
        $this->assertFalse($efc->issetHydrateEntityFromRequest());
        $efc->setHydrateEntityFromRequest(true);
        $this->assertSame(true, $efc->isHydrateEntityFromRequest());
        $efc->setHydrateEntityFromRequest(false);
        $this->assertSame(false, $efc->isHydrateEntityFromRequest());
        $this->assertTrue($efc->issetHydrateEntityFromRequest());
    }

    public function testSetGetParameterToInjectInto()
    {
        $efc = new EntityFactoryConfig();
        $this->assertFalse($efc->issetParameterToInjectInto());
        $efc->setParameterToInjectInto("bob");
        $this->assertSame("bob", $efc->getParameterToInjectInto());
        $this->assertTrue($efc->issetParameterToInjectInto());
    }

    public function testSetGetType()
    {
        $efc = new EntityFactoryConfig();
        $efc->setType(EntityFactoryConfig::TYPE_FETCH);
        $this->assertSame(EntityFactoryConfig::TYPE_FETCH, $efc->getType());
        $efc->setType(EntityFactoryConfig::TYPE_CREATE);
        $this->assertSame(EntityFactoryConfig::TYPE_CREATE, $efc->getType());
    }

    public function testValidateWithoutType()
    {
        $efc = new EntityFactoryConfig();
        $this->expectException(EntityFactoryConfigException::class);
        $this->expectExceptionMessage("Config must have a type");
        $efc->validate();
    }

    public function testValidateWithoutHydrate()
    {
        $efc = new EntityFactoryConfig();
        $efc->setType(EntityFactoryConfig::TYPE_FETCH);
        $this->expectException(EntityFactoryConfigException::class);
        $this->expectExceptionMessage("Config must know if entity will be hydrated with request params");
        $efc->validate();
    }

    public function testValidateWithoutEntity()
    {
        $efc = new EntityFactoryConfig();
        $efc->setType(EntityFactoryConfig::TYPE_FETCH);
        $efc->setHydrateEntityFromRequest(true);
        $this->expectException(EntityFactoryConfigException::class);
        $this->expectExceptionMessage("Config must have an EntityRequest class");
        $efc->validate();
    }

    public function testValidateWithoutInject()
    {
        $efc = new EntityFactoryConfig();
        $efc->setType(EntityFactoryConfig::TYPE_FETCH);
        $efc->setHydrateEntityFromRequest(true);
        $efc->setEntityRequest(Request::class);
        $this->expectException(EntityFactoryConfigException::class);
        $this->expectExceptionMessage("Config must have a parameter name for inject entity in action method");
        $efc->validate();
    }

    public function testValidateOk()
    {
        $efc = new EntityFactoryConfig();
        $efc->setType(EntityFactoryConfig::TYPE_FETCH);
        $efc->setHydrateEntityFromRequest(true);
        $efc->setEntityRequest(Request::class);
        $efc->setParameterToInjectInto('bob');
        $efc->validate();
        $this->assertTrue(true);
    }

    public function testCreateEntityRequest()
    {
        $efc = new EntityFactoryConfig();
        $e = Environment::mock();
        $r = \Slim\Http\Request::createFromEnvironment($e);
        $entityRequest = $this->getMockForAbstractClass(EntityRequestAbstract::class, [$r]);
        $efc->setEntityRequest(get_class($entityRequest));

        $c = new Container();
        $entityRequestInstance = $efc->createEntityRequest($r, $c);
        $this->assertInstanceOf($efc->getEntityRequest(), $entityRequestInstance);
    }
}
