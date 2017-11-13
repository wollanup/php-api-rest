<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 19/01/17
 * Time: 13:26
 */

namespace Eukles\Service\Router;

use Eukles\RouteMap\RouteMapInterface;

interface RouterInterface extends \Slim\Interfaces\RouterInterface
{

    /**
     * @param RouteInterface $resourceRoute
     *
     * @return RouteInterface
     */
    public function addResourceRoute(RouteInterface $resourceRoute
    ): RouteInterface;

    /**
     * @return RouteMapInterface[]
     */
    public function getRoutesMap(): array;
}
