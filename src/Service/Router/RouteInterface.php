<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 25/11/16
 * Time: 11:40
 */

namespace Eukles\Service\Router;

use Eukles\Entity\EntityFactoryConfig;
use Eukles\Entity\EntityRequestInterface;
use Eukles\Service\Router\Exception\RouteEmptyValueException;
use Psr\Container\ContainerInterface;
use Zend\Permissions\Acl\Role\RoleInterface;

interface RouteInterface extends \Slim\Interfaces\RouteInterface
{

    const POST = "POST";
    const GET = "GET";
    const PATCH = "PATCH";
    const PUT = "PUT";
    const DELETE = "DELETE";

    /**
     * @param string|RoleInterface $role
     *
     * @return RouteInterface
     */
    public function addRole($role): RouteInterface;

    /**
     * Add a status which may be sent by route
     *
     * Can be used to an api documentation to list all errors
     *
     * @param int    $status      HTTP status code
     * @param string $description Description, may be used in api documentation
     *
     * @return RouteInterface
     */
    public function addStatus(int $status, string $description = "");

    /**
     * @param RouterInterface $router
     *
     * @return mixed
     */
    public function bindToRouter(RouterInterface $router);

    /**
     * Route will create an entity based on given EntityFactoryConfig object
     *
     * @param EntityFactoryConfig $config
     *
     * @return RouteInterface
     */
    public function createEntity(EntityFactoryConfig $config): RouteInterface;

    /**
     * Mark route as deprecated (won't be documented)
     *
     * @return RouteInterface
     */
    public function deprecated(): RouteInterface;

    /**
     * Route will fetch an entity based on given EntityFactoryConfig object
     *
     * @param EntityFactoryConfig $config
     *
     * @return RouteInterface
     */
    public function fetchEntity(EntityFactoryConfig $config): RouteInterface;

    /**
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getActionClass(): string;

    /**
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getActionMethod(): string;

    /**
     * Return callable or string like "MyClass:myMethod"
     *
     * @return callable|string
     */
    public function getCallable();

    /**
     * @param string $paramName
     *
     * @return EntityFactoryConfig
     */
    public function getCreate(string $paramName): EntityFactoryConfig;

    /**
     * @return array
     */
    public function getCreates(): array;

    /**
     * @param string $paramName
     *
     * @return EntityFactoryConfig
     */
    public function getFetch(string $paramName): EntityFactoryConfig;

    /**
     * @return array
     */
    public function getFetches(): array;

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return mixed
     */
    public function getNameOfInjectedParam();

    /**
     * @return string
     */
    public function getPackage();

    /**
     * @deprecated will be remove when fetchCollection will be implemented
     * @return string|EntityRequestInterface
     */
    public function getRequestClass(): string;

    /**
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getResource(): string;

    /**
     * @return array
     */
    public function getRoles(): array;

    /**
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getVerb(): string;

    /**
     * @param string $paramName
     *
     * @return bool
     */
    public function hasCreate(string $paramName): bool;

    /**
     * @return bool
     */
    public function hasCreates(): bool;

    /**
     * @param string $paramName
     *
     * @return bool
     */
    public function hasFetch(string $paramName): bool;

    /**
     * @return bool
     */
    public function hasFetches(): bool;

    /**
     * @return bool
     */
    public function hasRoles(): bool;

    /**
     * @return bool
     */
    public function isDeprecated(): bool;

    /**
     * @return bool
     */
    public function isMakeCollection(): bool;

    /**
     * @param bool $forceFetch
     *
     * @return RouteInterface
     */
    public function makeCollection(bool $forceFetch = false): RouteInterface;

    /**
     * @param bool $forceFetch
     *
     * @deprecated
     * @see Route::fetchEntity()
     * @see Route::createEntity()

     * @return RouteInterface
     */
    public function makeInstance(bool $forceFetch = false): RouteInterface;

    /**
     * @param string $actionClass
     *
     * @return RouteInterface
     */
    public function setActionClass(string $actionClass): RouteInterface;

    /**
     * @param string $actionMethod
     *
     * @return RouteInterface
     */
    public function setActionMethod(string $actionMethod): RouteInterface;

    /**
     * @param ContainerInterface $container
     *
     * @return RouteInterface
     */
    public function setContainer(ContainerInterface $container);

    /**
     * @param string $identifier
     *
     * @return RouteInterface
     */
    public function setIdentifier(string $identifier): RouteInterface;

    /**
     * @param string $nameOfInjectedParam
     *
     * @deprecated
     * @return RouteInterface
     */
    public function setNameOfInjectedParam(string $nameOfInjectedParam
    ): RouteInterface;

    /**
     * @param string $package
     *
     * @return RouteInterface
     */
    public function setPackage(string $package): RouteInterface;

    /**
     * @param string $requestClass
     *
     * @deprecated will be remove when fetchCollection will be implemented
     * @return RouteInterface
     */
    public function setRequestClass(string $requestClass): RouteInterface;

    /**
     * @param array $roles
     *
     * @return RouteInterface
     */
    public function setRoles(array $roles): RouteInterface;

    /**
     * Set status code in case of success response
     *
     * @param int    $status      HTTP status code
     * @param string $description Description, may be used in api documentation
     *
     * @return RouteInterface
     *
     */
    public function setSuccessStatus(int $status, string $description = "");

    /**
     * @param string $verb
     *
     * @return RouteInterface
     */
    public function setVerb(string $verb): RouteInterface;

    /**
     * When route makes an instance, it can use request parameters to alter instance.
     *
     * Enabled by default.
     *
     * @param $bool
     *
     * @deprecated
     * @return RouteInterface
     */
    public function useRequest(bool $bool): RouteInterface;
}
