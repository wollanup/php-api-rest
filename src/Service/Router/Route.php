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
use Eukles\Entity\EntityFactoryConfigException as EntityFactoryConfigExceptionAlias;
use Eukles\Entity\EntityRequestInterface;
use Eukles\Entity\Middleware\CollectionFetch;
use Eukles\Entity\Middleware\EntityMiddleware;
use Eukles\RouteMap\RouteMapInterface;
use Eukles\Service\Router\Exception\RouteEmptyValueException;
use Eukles\Service\Router\Middleware\SuccessHeaderLocationMiddleware;
use Eukles\Service\Router\Middleware\SuccessStatusMiddleware;
use Eukles\Slim\DeferredCallable;
use InvalidArgumentException;
use RuntimeException;
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
     * @var array
     */
    protected $entities = [];
    /**
     * @var bool
     */
    protected $deprecated = false;
    /**
     * @deprecated
     * @var bool
     */
    protected $instanceForceFetch = false;
    /**
     * Array of status sent by this route
     *
     * @var HttpStatus[]
     */
    protected $statuses = [];
    /**
     * Privilege name of this route
     *
     * @var string
     */
    protected $privilege;
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
     * @deprecated will be remove when fetchCollection will be implemented
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
     * @param int $status
     * @param string $description
     *
     * @param bool $isMainSuccess
     *
     * @return $this
     */
    public function addStatus(int $status, string $description = "", $isMainSuccess = false)
    {
        $this->statuses[$status] = [$description, false];

        $this->statuses[$status] = HttpStatus::create()
            ->setStatus($status)
            ->setDescription($description)
            ->setMainSuccess($isMainSuccess);

        return $this;
    }

    /**
     * @param RouterInterface $router
     *
     * @return mixed|void
     * @throws RouteEmptyValueException
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
     * @return string
     * @throws RouteEmptyValueException
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

    /**
     * @return string
     * @throws RouteEmptyValueException
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

    /**
     * @return boolean
     */
    public function isMakeCollection(): bool
    {
        return $this->collectionFromPks;
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
     * @inheritdoc
     */
    public function deprecated(): RouteInterface
    {
        $this->deprecated = true;

        return $this;
    }

    /**
     * @param string $paramName
     *
     * @return EntityFactoryConfig
     */
    public function getEntityConfig(string $paramName): EntityFactoryConfig
    {
        if (!$this->hasEntity($paramName)) {
            throw new RuntimeException("Unknown entity parameter");
        }

        return $this->entities[$paramName];
    }

    /**
     * @param string $paramName
     *
     * @return bool
     */
    public function hasEntity(string $paramName): bool
    {
        return isset($this->entities[$paramName]);
    }

    /**
     * @return EntityFactoryConfig[]
     */
    public function getEntities(): array
    {
        return $this->entities;
    }

    /**
     * @return null|string
     * @throws RouteEmptyValueException
     */
    public function getName()
    {
        return sprintf('%s:%s', $this->getResource(), $this->getActionMethod());
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
     * @return mixed|string
     */
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
     * @throws RouteEmptyValueException
     */
    public function getPattern(): string
    {
        return $this->required($this->pattern);
    }

    /**
     * @deprecated will be remove when fetchCollection will be implemented
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getRequestClass(): string
    {
        return $this->required($this->requestClass);
    }

    /**
     * @param string $requestClass
     *
     * @deprecated
     * @return RouteInterface
     */
    public function setRequestClass(string $requestClass): RouteInterface
    {
        $this->requestClass = $requestClass;

        return $this;
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
     * @param string|RoleInterface $role
     *
     * @return RouteInterface
     */
    public function addRole($role): RouteInterface
    {
        if (is_string($role)) {
            $this->roles[] = new GenericRole($role);
        } elseif (!$role instanceof RoleInterface) {
            throw new InvalidArgumentException(
                'addRole() expects $role to be of type Zend\Permissions\Acl\Role\RoleInterface'
            );
        }

        return $this;
    }

    /**
     * @return HttpStatus[]
     */
    public function getStatuses(): array
    {
        return $this->statuses;
    }

    /**
     * @return bool
     */
    public function hasEntities(): bool
    {
        return !empty($this->entities);
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
    public function isDeprecated(): bool
    {
        return $this->deprecated;
    }

    /**
     * @param bool $forceFetch
     *
     * @deprecated Not replaced yet
     * @return RouteInterface
     */
    public function makeCollection(bool $forceFetch = false): RouteInterface
    {
        $this->collectionFromPks = true;
        $this->instanceForceFetch = $forceFetch;

        return $this;
    }

    /**
     * @param bool $forceFetch
     *
     * @return RouteInterface
     * @throws RouteEmptyValueException
     * @throws EntityFactoryConfigExceptionAlias
     * @deprecated
     * @see Route::fetchEntity()
     * @see Route::createEntity()
     */
    public function makeInstance(bool $forceFetch = false): RouteInterface
    {
        if ($forceFetch || $this->getVerb() !== 'POST') {
            $this->fetchEntity(EntityFactoryConfig::create());
        } else {
            $this->createEntity(EntityFactoryConfig::create());
        }
        $this->instanceFromPk = true;
        $this->instanceForceFetch = $forceFetch;

        return $this;
    }

    /**
     * @return string
     * @throws RouteEmptyValueException
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
     *
     * @param EntityFactoryConfig $config
     *
     * @return RouteInterface
     * @throws EntityFactoryConfigExceptionAlias
     * @throws RouteEmptyValueException
     */
    public function fetchEntity(EntityFactoryConfig $config): RouteInterface
    {

        # Auto set type
        $config->setType(EntityFactoryConfig::TYPE_FETCH);
        # Auto determine if we hydrate from request or not
        if (!$config->issetHydrateEntityFromRequest()) {
            $config->setHydrateEntityFromRequest($this->getVerb() !== 'GET');
        }
        # Auto add EntityRequest if not specified
        if (!$config->issetEntityRequest()) {
            $config->setEntityRequest($this->requestClass);
        }
        # Auto determine name of parameter to add
        if (!$config->issetParameterToInjectInto()) {
            /** @var EntityRequestInterface $entityRequestClass */
            $entityRequestClass = $config->getEntityRequest();
            $config->setParameterToInjectInto($entityRequestClass::getNameOfParameterToAdd(false));
        }

        # Make sure config is clean
        $config->validate();

        $this->entities[$config->getParameterToInjectInto()] = $config;

        return $this->add(new EntityMiddleware($this->getContainer(), $config));
    }

    /**
     * @param EntityFactoryConfig $config
     *
     * @return RouteInterface
     * @throws EntityFactoryConfigExceptionAlias
     */
    public function createEntity(EntityFactoryConfig $config): RouteInterface
    {
        # Auto set type
        $config->setType(EntityFactoryConfig::TYPE_CREATE);
        # Auto determine if we hydrate from request or not
        if (!$config->issetHydrateEntityFromRequest()) {
            $config->setHydrateEntityFromRequest(true);
        }
        # Auto add EntityRequest if not specified
        if (!$config->issetEntityRequest()) {
            $config->setEntityRequest($this->requestClass);
        }
        # Auto determine name of parameter to add
        if (!$config->issetParameterToInjectInto()) {
            /** @var EntityRequestInterface $entityRequestClass */
            $entityRequestClass = $config->getEntityRequest();
            $config->setParameterToInjectInto($entityRequestClass::getNameOfParameterToAdd(false));
        }

        # Make sure config is clean
        $config->validate();

        $this->entities[$config->getParameterToInjectInto()] = $config;

        return $this->add(new EntityMiddleware($this->getContainer(), $config));
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
     * Set status code in case of success response
     *
     * @param int $status
     * @param string $description
     *
     * @return RouteInterface
     *
     */
    public function setSuccessStatus(int $status, string $description = ""): RouteInterface
    {
        $this->statuses[$status] = HttpStatus::create()
            ->setStatus($status)
            ->setDescription($description)
            ->setMainSuccess(true);

        /**
         * This middleware is an "AFTER" middleware, so add it first to be run last...
         */
        return $this->addFirst(new SuccessStatusMiddleware($status));
    }

    /**
     * @param callable|string $callable
     *
     * @return $this
     */
    public function addFirst($callable)
    {
        array_unshift($this->middleware, new DeferredCallable($callable, $this->container));

        return $this;
    }

    //    public function setPaginateHeaders()
    //    {
    //        return $this->addFirst(new SuccessHeaderLocationMiddleware($location, $config));
    //    }

    /**
     * Add a Location header to the response
     *
     * Can take a placeholder to replace a variable by an entity getter
     * e.g.
     * ```php
     * '/resource/{id}'
     * ```
     * will be replaced by
     * ```php
     * '/resource/' . $entity->getId()
     * ```
     *
     * @param string $location
     * @param EntityFactoryConfig $config
     *
     * @param int $status
     *
     * @return RouteInterface
     */
    public function setSuccessLocationHeader(
        string $location,
        EntityFactoryConfig $config,
        int $status = 302
    ): RouteInterface {
        return $this->addFirst(new SuccessHeaderLocationMiddleware($location, $config, $status));
    }

    /**
     * @return bool
     */
    public function hasPrivilege(): bool
    {
        return $this->privilege !== null;
    }

    /**
     * @return string
     */
    public function getPrivilege(): string
    {
        return $this->privilege;
    }

    /**
     * @param string $privilege
     * @return RouteInterface
     */
    public function setPrivilege(string $privilege): RouteInterface
    {
        $this->privilege = $privilege;

        return $this;
    }
}
