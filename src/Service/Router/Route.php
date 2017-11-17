<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 25/11/16
 * Time: 11:40
 */

namespace Eukles\Service\Router;

use Eukles\Container\ContainerInterface;
use Eukles\Container\ContainerTrait;
use Eukles\Entity\EntityFactoryConfig;
use Eukles\Entity\Middleware\CollectionFetch;
use Eukles\Entity\Middleware\EntityCreate;
use Eukles\Entity\Middleware\EntityFetch;
use Eukles\RouteMap\RouteMapInterface;
use Eukles\Service\Router\Exception\RouteEmptyValueException;
use Eukles\Slim\DeferredCallable;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

/**
 * Class Route
 *
 * @method ContainerInterface getContainer()
 * @property ContainerInterface $container
 * @package Eukles\Service\Router
 */
class Route extends \Slim\Route implements RouteInterface
{

    use ContainerTrait;
    /**
     * @var
     */
    protected $collectionFromPks = false;
    /**
     * @var bool
     */
    protected $createEntity = false;
    /**
     * @var bool
     */
    protected $deprecated = false;
    /**
     * @var bool
     */
    protected $fetchEntity = false;
    /**
     * @deprecated
     * @var bool
     */
    protected $instanceForceFetch = false;
    /**
     * @deprecated
     * @var bool
     */
    protected $useRequest = true;
    /**
     * @var string
     */
    private $actionClass;
    /**
     * @var string
     */
    private $actionMethod;
    /**
     * @var bool
     * @deprecated
     */
    private $instanceFromPk = false;
    /**
     * @deprecated
     * @var string
     */
    private $nameOfInjectedParam;
    /**
     * @var string
     */
    private $package;
    /**
     * @var string
     */
    private $requestClass;
    /**
     * @var string
     */
    private $resource;
    /**
     * @var array
     */
    private $roles;
    /**
     * @var string
     */
    private $verb;

    public function __construct(RouteMapInterface $routeMap, $method)
    {
        parent::__construct($method, null, null, [], 0);
        $this->container = $routeMap->getContainer();
        // According to RFC methods are defined in uppercase (See RFC 7231)
        $this->methods = array_map("strtoupper", $this->methods);
    }

    /**
     * @param callable|string $callable
     *
     * @return $this
     */
    public function add($callable)
    {
        $this->middleware[] = new DeferredCallable($callable, $this->container);

        return $this;
    }

    /**
     * @param string|RoleInterface $role
     *
     * @return RouteInterface
     */
    public function addRole($role): RouteInterface
    {
        if (is_string($role)) {
            $this->roles[] = new GenericRole($role);
        } elseif (!$role instanceof RoleInterface) {
            throw new \InvalidArgumentException(
                'addRole() expects $role to be of type Zend\Permissions\Acl\Role\RoleInterface'
            );
        }

        return $this;
    }

    /**
     * @param RouterInterface $router
     *
     * @return mixed|void
     */
    public function bindToRouter(RouterInterface $router)
    {
        $this->callable = sprintf('%s:%s', $this->getActionClass(),
            $this->getActionMethod());

        // TODO Remove and use createCollection() / fetchCollection()
        if ($this->isMakeCollection()) {
            $this->add(new CollectionFetch($this->container, $this));
        }

        $router->addResourceRoute($this);
    }

    /**
     * @param string      $entityRequestClass
     * @param string|null $injectInActionParameterName
     * @param bool        $hydrateEntityFromRequest
     */
    public function createEntity(
        string $entityRequestClass,
        string $injectInActionParameterName = null,
        bool $hydrateEntityFromRequest = true
    ) {
        $this->createEntity = true;

        $config = $this->buildEntityFactoryConfig(
            $entityRequestClass,
            null,
            $injectInActionParameterName,
            $hydrateEntityFromRequest

        );
        $this->add(new EntityCreate($config));
    }

    /**
     * Will inject an instance of Entity (Active Record) in Action method
     *
     *
     * @param string      $entityRequestClass
     * @param string|null $injectInActionParameterName
     * @param bool        $hydrateEntityFromRequest
     */
    public function createEntityFromPk(
        string $entityRequestClass,
        string $injectInActionParameterName = null,
        bool $hydrateEntityFromRequest = true
    ) {
        $this->createEntity(
            $entityRequestClass,
            $injectInActionParameterName,
            $hydrateEntityFromRequest
        );
    }

    /**
     * @inheritdoc
     */
    public function deprecated(): RouteInterface
    {
        $this->deprecated = true;

        return $this;
    }

    /**
     * @param string      $requestParameter
     * @param string      $entityRequestClass
     * @param string|null $injectInActionParameterName
     * @param bool        $hydrateEntityFromRequest
     */
    public function fetchEntityFromParam(
        string $requestParameter,
        string $entityRequestClass,
        string $injectInActionParameterName = null,
        bool $hydrateEntityFromRequest = true
    ) {
        $this->fetchEntity = true;

        $config = $this->buildEntityFactoryConfig(
            $entityRequestClass,
            $requestParameter,
            $injectInActionParameterName,
            $hydrateEntityFromRequest

        );
        $this->add(new EntityFetch($config));
    }

    public function fetchEntityFromPk(
        string $entityRequestClass,
        string $injectInActionParameterName = null,
        bool $hydrateEntityFromRequest = true
    ) {
        // TODO multiple PKS
//        $pks = $entityRequest->getTableMap()->getPrimaryKeys();
//        foreach ( as $index => $index) {
//
//        }
        $this->fetchEntityFromParam(
            'id',
            $entityRequestClass,
            $injectInActionParameterName,
            $hydrateEntityFromRequest
        );
    }

    /**
     * @return string
     */
    public function getActionClass(): string
    {
        return $this->required($this->actionClass);
    }

    /**
     * @param string $actionClass
     *
     * @return RouteInterface
     */
    public function setActionClass(string $actionClass): RouteInterface
    {
        $this->actionClass = $actionClass;

        return $this;
    }

    /**
     * @return string
     */
    public function getActionMethod(): string
    {
        return $this->required($this->actionMethod);
    }

    /**
     * @param string $actionMethod
     *
     * @return RouteInterface
     */
    public function setActionMethod(string $actionMethod): RouteInterface
    {
        $this->actionMethod = $actionMethod;

        return $this;
    }

    public function getName()
    {
        return sprintf('%s:%s', $this->getResource(), $this->getActionMethod());
    }

    public function getNameOfInjectedParam()
    {
        return $this->nameOfInjectedParam;
    }

    /**
     * @param string $nameOfInjectedParam
     *
     * @deprecated
     * @return RouteInterface
     */
    public function setNameOfInjectedParam(string $nameOfInjectedParam
    ): RouteInterface {
        $this->nameOfInjectedParam = $nameOfInjectedParam;

        return $this;
    }

    public function getPackage()
    {
        return $this->package;
    }

    /**
     * @param string $package
     *
     * @return RouteInterface
     */
    public function setPackage(string $package): RouteInterface
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern(): string
    {
        return $this->required($this->pattern);
    }

    /**
     * @return string
     */
    public function getRequestClass(): string
    {
        return $this->required($this->requestClass);
    }

    /**
     * @param string $requestClass
     *
     * @return RouteInterface
     */
    public function setRequestClass(string $requestClass): RouteInterface
    {
        $this->requestClass = $requestClass;

        return $this;
    }

    /**
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getResource(): string
    {
        return $this->required($this->resource);
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     *
     * @return RouteInterface
     */
    public function setRoles(array $roles): RouteInterface
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getVerb(): string
    {
        return $this->required($this->getMethods()[0]);
    }

    /**
     * @param string $verb UPPERCASE http method
     *
     * @return RouteInterface
     */
    public function setVerb(string $verb): RouteInterface
    {
        // According to RFC methods are defined in uppercase (See RFC 7231)
        $this->verb = strtoupper($verb);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasRoles(): bool
    {
        return false === empty($this->roles);
    }

    /**
     * @return bool
     */
    public function isCreateEntity(): bool
    {
        return $this->createEntity;
    }

    /**
     * @return bool
     */
    public function isDeprecated(): bool
    {
        return $this->deprecated;
    }

    /**
     * @return bool
     */
    public function isFetchEntity(): bool
    {
        return $this->fetchEntity;
    }

    /**
     * @return boolean
     */
    public function isMakeCollection(): bool
    {
        return $this->collectionFromPks;
    }

    /**
     * @param bool $forceFetch
     *
     * @return RouteInterface
     */
    public function makeCollection(bool $forceFetch = false): RouteInterface
    {
        $this->collectionFromPks  = true;
        $this->instanceForceFetch = $forceFetch;

        return $this;
    }

    /**
     * @param bool $forceFetch
     *
     * @return RouteInterface
     */
    public function makeInstance(bool $forceFetch = false): RouteInterface
    {
        $this->instanceFromPk     = true;
        $this->instanceForceFetch = $forceFetch;

        return $this;
    }

    /**
     * @param string $identifier
     *
     * @return RouteInterface
     */
    public function setIdentifier(string $identifier): RouteInterface
    {
        $this->identifier = $identifier;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function useRequest(bool $bool): RouteInterface
    {
        $this->useRequest = $bool;

        return $this;
    }

    /**
     * @param string      $entityRequestClass
     * @param string|null $requestParameter
     * @param string|null $injectInActionParameterName
     * @param bool        $hydrateEntityFromRequest
     *
     * @return EntityFactoryConfig
     */
    private function buildEntityFactoryConfig(
        string $entityRequestClass,
        string $requestParameter = null,
        string $injectInActionParameterName = null,
        bool $hydrateEntityFromRequest = true
    ): EntityFactoryConfig {

        $config = new EntityFactoryConfig($this->getContainer());

        if ($requestParameter) {
            $config->setRequestParameter($requestParameter);
        }

        $config
            ->setEntityRequest(new $entityRequestClass($this->getContainer()))
            ->setParameterToInjectInto($injectInActionParameterName
                ?: $this->nameOfInjectedParam)
            ->setHydrateEntityFromRequest($hydrateEntityFromRequest
                ?: $this->useRequest);

        return $config;
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     * @throws RouteEmptyValueException
     */
    private function required($value)
    {
        if (empty($value)) {
            throw new RouteEmptyValueException('Missing value');
        }

        return $value;
    }
}
