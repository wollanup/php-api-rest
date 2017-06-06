<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 25/11/16
 * Time: 11:40
 */

namespace Eukles\Service\Router;

use Eukles\Container\ContainerInterface;
use Eukles\RouteMap\RouteMapInterface;
use Eukles\Service\Router\Exception\RouteEmptyValueException;
use Zend\Permissions\Acl\Role\GenericRole;
use Zend\Permissions\Acl\Role\RoleInterface;

class Route extends \Slim\Route implements RouteInterface
{
    
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
    
    public function __construct(RouteMapInterface $RouteMap, $method)
    {
        parent::__construct($method, null, null, [], 0);
        $this->container = $RouteMap->getContainer();
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
            $route = $this;
            if ($this->isMakeInstanceCreate()) {
                # POST : create
                $this->add(function ($request, $response, $next) use ($route) {
                    $requestClass = $route->getRequestClass();
                    /** @var ContainerInterface $this */
                    $response = $this->getEntityFactory()->create(
                        new $requestClass($this),
                        $request,
                        $response,
                        $next,
                        $route->getNameOfInjectedParam()
                    );
                    
                    return $response;
                });
            } else {
                # OTHERS : fetch
                $this->add(function ($request, $response, $next) use ($route) {
                    $requestClass = $route->getRequestClass();
                    /** @var ContainerInterface $this */
                    $response = $this->getEntityFactory()->fetch(
                        new $requestClass($this),
                        $request,
                        $response,
                        $next,
                        $route->getNameOfInjectedParam()
                    );
                    
                    return $response;
                });
            }
        } elseif ($this->isMakeCollection()) {
            $route = $this;
            $this->add(function ($request, $response, $next) use ($route) {
                $requestClass = $route->getRequestClass();
                /** @var ContainerInterface $this */
                $response = $this->getEntityFactory()->fetchCollection(
                    new $requestClass($this),
                    $request,
                    $response,
                    $next,
                    $route->getNameOfInjectedParam()
                );
    
                return $response;
            });
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
