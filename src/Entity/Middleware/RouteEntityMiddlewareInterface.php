<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 12/10/17
 * Time: 11:30
 */

namespace Eukles\Entity\Middleware;

use Eukles\Container\ContainerInterface;
use Eukles\Entity\EntityFactoryConfig;
use Eukles\Service\Router\Middleware\RouteMiddlewareInterface;
use Psr\Http\Message\ResponseInterface;

interface RouteEntityMiddlewareInterface extends RouteMiddlewareInterface
{

    /**
     * RouteEntityMiddleware constructor.
     *
     * @param ContainerInterface  $container
     * @param EntityFactoryConfig $config
     */
    public function __construct(ContainerInterface $container, EntityFactoryConfig $config);

    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next): ResponseInterface;
}

