<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 25/11/16
 * Time: 11:40
 */

namespace Eukles\Service\Router;

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
     * @param RouterInterface $router
     *
     * @return mixed
     */
    public function bindToRouter(RouterInterface $router);

    /**
     * Mark route as deprecated (won't be documented)
     *
     * @return RouteInterface
     */
    public function deprecated(): RouteInterface;

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
     *
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
     * @return RouteInterface
     */
    public function useRequest(bool $bool): RouteInterface;

    /**
     * Tells if route will create an entity
     *
     * @return bool
     */
    public function isCreateEntity(): bool;

    /**
     * Tells if route will fetch an entity
     *
     * @return bool
     */
    public function isFetchEntity(): bool;

}
