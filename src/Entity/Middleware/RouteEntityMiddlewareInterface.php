<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 12/10/17
 * Time: 11:30
 */

namespace Eukles\Entity\Middleware;

use Eukles\Container\ContainerInterface;
use Eukles\Service\Router\RouteInterface;
use Psr\Http\Message\ResponseInterface;

interface RouteEntityMiddlewareInterface
{
    
    /**
     * RouteEntityMiddleware constructor.
     *
     * @param ContainerInterface $container
     * @param RouteInterface     $route
     */
    public function __construct(ContainerInterface $container, RouteInterface $route);
    
    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next): ResponseInterface;
}

