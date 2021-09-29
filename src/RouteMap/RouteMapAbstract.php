<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/11/16
 * Time: 17:15
 */

namespace Eukles\RouteMap;

use Eukles\Action\ActionInterface;
use Eukles\Container\ContainerTrait;
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

    use ContainerTrait;

    protected int $apiVersion = 1;
    /**
     * @var string|ActionInterface
     */
    protected $actionClass = '';
    /**
     * @var string
     */
    protected $packageName = '';
    /**
     * @var string|EntityRequestInterface
     */
    protected $requestClass = '';
    /**
     * @var string
     */
    protected $resourceName = '';
    /**
     * @var string
     */
    protected $routesPrefix = '';

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
     * @return int
     */
    public function getApiVersion(): int
    {
        return $this->apiVersion;
    }

    /**
     * @param int $apiVersion
     * @return RouteMapAbstract
     */
    public function setApiVersion(int $apiVersion): RouteMapAbstract
    {
        $this->apiVersion = $apiVersion;
        return $this;
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
     * @param $method
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function add($method, $pattern): RouteInterface
    {
        $route = new Route($this, $method);
        $route->setContainer($this->container);
        if ('' !== $this->actionClass) {
            $route->setActionClass($this->actionClass);
        }
        if ('' !== $this->requestClass) {
            $route->setRequestClass($this->requestClass);
        }
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
        if ($this->getApiVersion() > 1) {
            # Add version before resource
            array_unshift($prefixes, 'v' . $this->getApiVersion());
        }

        $pattern = $this->trailingSlash($pattern);
        $pattern = implode('/', $prefixes) . $pattern;
        $pattern = '/' . ltrim($pattern, '/');
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
    public function delete($pattern): RouteInterface
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
    public function get($pattern): RouteInterface
    {
        return $this->add(Route::GET, $pattern);
    }

    /**
     * @inheritdoc
     */
    public function getActionClass(): string
    {
        return $this->actionClass;
    }

    /**
     * @return string
     */
    public function getPackage(): string
    {
        return (string)(($this->isSubResourceOfPackage()) ? $this->packageName : $this->resourceName);
    }

    /**
     * @return string
     */
    public function getPrefix(): string
    {
        return $this->routesPrefix;
    }

    /**
     * @return string
     */
    public function getResource(): string
    {
        return $this->resourceName;
    }

    /**
     * @return bool
     */
    public function hasPackage(): bool
    {
        return '' !== $this->packageName;
    }

    /**
     * @return bool
     */
    public function isSubResourceOfPackage(): bool
    {
        if (!$this->hasPackage()) {
            return false;
        }

        return $this->resourceName !== $this->packageName;
    }

    /**
     * self::add('PATCH', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function patch($pattern): RouteInterface
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
    public function post($pattern): RouteInterface
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
    public function put($pattern): RouteInterface
    {
        return $this->add(Route::PUT, $pattern);
    }

    final public function registerRoutes(RouterInterface $router)
    {
        foreach ($this->data as $route) {
            $route->bindToRouter($router);
        }
    }

    /**
     * @param $routeName
     *
     * @return string
     */
    private function trailingSlash($routeName)
    {
        $routeName = rtrim($routeName, ']');
        $routeName = rtrim($routeName, '[/');
        $missing = substr_count($routeName, '[') - substr_count($routeName, ']');
        $routeName .= '[/]' . str_repeat(']', $missing);

        return $routeName;
    }
}
