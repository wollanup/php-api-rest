<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 24/11/16
 * Time: 17:12
 */

namespace Eukles\RouteMap;

use Eukles\Action\ActionInterface;
use Eukles\Service\Router\RouteInterface;
use Eukles\Service\Router\RouterInterface;
use Psr\Container\ContainerInterface;

interface RouteMapInterface extends \Iterator
{

    public function __construct(ContainerInterface $container);

    /**
     * Adds a route to this RouteMap
     *
     * @param $method
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function add($method, $pattern): RouteInterface;

    /**
     * self::add('DELETE', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function delete($pattern): RouteInterface;

    /**
     * self::add('GET', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function get($pattern): RouteInterface;

    /**
     * @return string|ActionInterface
     */
    public function getActionClass(): string;

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * @return string
     */
    public function getPackage(): string;

    /**
     * @return string
     */
    public function getPrefix(): string;

    /**
     * @return string
     */
    public function getResource(): string;

    /**
     * @return bool
     */
    public function hasPackage(): bool;

    /**
     * @return bool
     */
    public function isSubResourceOfPackage(): bool;

    /**
     * self::add('PATCH', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function patch($pattern): RouteInterface;

    /**
     * self::add('POST', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function post($pattern): RouteInterface;

    /**
     * self::add('PUT', $pattern) shortcut
     *
     * @param $pattern
     *
     * @return RouteInterface
     */
    public function put($pattern): RouteInterface;

    /**
     * Map all Routes contained in this RouteMap to th given Router Object
     *
     * @param RouterInterface $router
     */
    public function registerRoutes(RouterInterface $router);
}
