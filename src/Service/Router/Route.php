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
 * @property ContainerInterface $container
 * @package Eukles\Service\Router
 */
class Route extends \Slim\Route implements RouteInterface
{
    
    use ContainerTrait;
    
    protected $collectionFromPks;
    /**
     * @var bool
     */
    protected $deprecated = false;
    /**
     * @var bool
     */
    protected $instanceForceFetch = false;
    /**
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
     */
    private $instanceFromPk = false;
    /**
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
     * @param string|RoleInterface $role
     *
     * @return RouteInterface
     */
    public function addRole($role)
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
        $this->callable = sprintf('%s:%s', $this->getActionClass(), $this->getActionMethod());
        if ($this->isMakeInstance()) {
            if ($this->isMakeInstanceCreate()) {
                # POST : create
                $this->add(new EntityCreate($this->container, $this));
            } else {
                # OTHERS : fetch
                $this->add(new EntityFetch($this->container, $this));
            }
        } elseif ($this->isMakeCollection()) {
            $this->add(new CollectionFetch($this->container, $this));
        }
        
        $router->addResourceRoute($this);
    }
    
    /**
     * @inheritdoc
     */
    public function deprecated()
    {
        $this->deprecated = true;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getActionClass()
    {
        return $this->required($this->actionClass);
    }
    
    /**
     * @param string $actionClass
     *
     * @return RouteInterface
     */
    public function setActionClass($actionClass)
    {
        $this->actionClass = $actionClass;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getActionMethod()
    {
        return $this->required($this->actionMethod);
    }
    
    /**
     * @param string $actionMethod
     *
     * @return RouteInterface
     */
    public function setActionMethod($actionMethod)
    {
        $this->actionMethod = $actionMethod;
        
        return $this;
    }
    
    public function getName()
    {
        return sprintf('%s:%s', $this->getResource(), $this->getActionMethod());
    }
    
    /**
     * @return string
     */
    public function getNameOfInjectedParam()
    {
        return $this->nameOfInjectedParam;
    }
    
    /**
     * @param string $nameOfInjectedParam
     *
     * @return RouteInterface
     */
    public function setNameOfInjectedParam($nameOfInjectedParam)
    {
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
    public function setPackage($package)
    {
        $this->package = $package;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->required($this->pattern);
    }
    
    /**
     * @return string
     */
    public function getRequestClass()
    {
        return $this->required($this->requestClass);
    }
    
    /**
     * @param string $requestClass
     *
     * @return RouteInterface
     */
    public function setRequestClass($requestClass)
    {
        $this->requestClass = $requestClass;
        
        return $this;
    }
    
    /**
     * @return string
     * @throws RouteEmptyValueException
     */
    public function getResource()
    {
        return $this->required($this->resource);
    }
    
    /**
     * @return array
     */
    public function getRoles()
    {
        return $this->roles;
    }
    
    /**
     * @param array $roles
     *
     * @return RouteInterface
     */
    public function setRoles(array $roles)
    {
        foreach ($roles as $role) {
            $this->addRole($role);
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getVerb()
    {
        return $this->required($this->getMethods()[0]);
    }
    
    /**
     * @param string $verb UPPERCASE http method
     *
     * @return RouteInterface
     */
    public function setVerb($verb)
    {
        // According to RFC methods are defined in uppercase (See RFC 7231)
        $this->verb = strtoupper($verb);
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function hasRoles()
    {
        return false === empty($this->roles);
    }
    
    /**
     * @inheritdoc
     */
    public function hasToUseRequest()
    {
        return $this->useRequest;
    }
    
    /**
     * @return RouteInterface
     */
    public function instanceFetch()
    {
        $this->instanceFromPk = true;
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isDeprecated()
    {
        return $this->deprecated;
    }
    
    /**
     * @return boolean
     */
    public function isMakeCollection()
    {
        return $this->collectionFromPks;
    }
    
    /**
     * @return boolean
     */
    public function isMakeInstance()
    {
        return $this->instanceFromPk;
    }
    
    /**
     * @return bool
     */
    public function isMakeInstanceCreate()
    {
        return $this->getVerb() === Route::POST && !$this->instanceForceFetch;
    }
    
    /**
     * @return bool
     */
    public function isMakeInstanceFetch()
    {
        return !$this->isMakeInstanceCreate();
    }
    
    /**
     * @param bool $forceFetch
     *
     * @return RouteInterface
     */
    public function makeCollection($forceFetch = false)
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
    public function makeInstance($forceFetch = false)
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
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
        
        return $this;
    }
    
    /**
     * @inheritdoc
     */
    public function useRequest($bool)
    {
        $this->useRequest = $bool;
        
        return $this;
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
