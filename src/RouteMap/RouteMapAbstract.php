<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/11/16
 * Time: 17:15
 */

namespace Eukles\RouteMap;

use Eukles\Action\ActionInterface;
use Eukles\Entity\EntityRequestInterface;
use Eukles\Service\Router\Route;
use Eukles\Service\Router\RouteInterface;
use Eukles\Service\Router\RouterInterface;
use Eukles\Util\DataIterator;
use Psr\Container\ContainerInterface;

/**
 * Class RouteMapAbstract
 *
 * @property RouteInterface[] $data
 *
 * @package Eukles\Service\RouteMap
 */
abstract class RouteMapAbstract extends DataIterator implements RouteMapInterface
{
    
    /**
     * @var string|ActionInterface
     */
    protected $actionClass;
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var string
     */
    protected $packageName;
    /**
     * @var string|EntityRequestInterface
     */
    protected $requestClass;
    /**
     * @var string
     */
    protected $resourceName;
    /**
     * @var string
     */
    protected $routesPrefix;
    
    /**
     * RouteMapAbstract constructor.
     *
     * @param ContainerInterface $container
     */
    final public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->initialize();
    }
    
    /**
     * @inheritdoc
     */
    public function getActionClass()
    {
        return $this->actionClass;
    }
    
    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * @return string
     */
    public function getPackage()
    {
        return ($this->isSubResourceOfPackage()) ? $this->packageName : $this->resourceName;
    }
    
    final public function registerRoutes(RouterInterface $router)
    {
        foreach ($this->data as $Route) {
            $Route->bindToRouter($router);
        }
    }
    
    /**
     * @param $method
     * @param $pattern
     *
     * @return RouteInterface
     */
    final protected function add($method, $pattern)
    {
        $route = new Route($this, $method);
        $route->setContainer($this->container);
        $route->setActionClass($this->actionClass);
        $route->setRequestClass($this->requestClass);
        $route->setPackage($this->getPackage());
        $prefixes = [$this->resourceName];
        if ($this->isSubResourceOfPackage()) {
            # Add package before resource
            array_unshift($prefixes, $this->packageName);
        }
        if ($this->routesPrefix) {
            # Add prefix before resource
            array_unshift($prefixes, $this->routesPrefix);
        }
        $pattern = $this->trailingSlash('/' . implode('/', $prefixes) . $pattern);
        $route->setPattern($pattern);
        
        $this->data[] = $route;
        
        return $route;
    }
    
    /**
     * self::add('DELETE', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    final protected function delete($pattern)
    {
        return $this->add(Route::DELETE, $pattern);
    }
    
    /**
     * self::add('GET', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    final protected function get($pattern)
    {
        return $this->add(Route::GET, $pattern);
    }
    
    /**
     * Routes
     *
     * ```
     * $this->add('GET', '/{id:[0-9]+}')
     *     ->setRoles(['user',])
     *     ->setActionClass(OtherClass::class)
     *     ->setActionMethod('get');
     *```
     *
     * @return mixed
     */
    abstract protected function initialize();
    
    /**
     * self::add('PATCH', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    final protected function patch($pattern)
    {
        return $this->add(Route::PATCH, $pattern);
    }
    
    /**
     * self::add('POST', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    final protected function post($pattern)
    {
        return $this->add(Route::POST, $pattern);
    }
    
    /**
     * self::add('PUT', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    final protected function put($pattern)
    {
        return $this->add(Route::PUT, $pattern);
    }
    
    private function hasPackage()
    {
        return null !== $this->packageName;
    }
    
    private function isSubResourceOfPackage()
    {
        if (!$this->hasPackage()) {
            return false;
        }
        
        return $this->resourceName !== $this->packageName;
    }
    
    private function trailingSlash($routeName)
    {
        if (substr($routeName, -1) === ']') {
            $routeName = rtrim($routeName, ']');
            $routeName .= '/]';
            
            return $routeName;
        }
        
        if (substr($routeName, -1) !== '/') {
            $routeName .= '/';
        }
        
        return $routeName;
    }
}
