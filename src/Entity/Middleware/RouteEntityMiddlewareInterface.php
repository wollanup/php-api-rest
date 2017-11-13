<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 12/10/17
 * Time: 11:30
 */

namespace Eukles\Entity\Middleware;

use Eukles\Entity\EntityFactoryConfig;
use Psr\Http\Message\ResponseInterface;

interface RouteEntityMiddlewareInterface
{

    /**
     * RouteEntityMiddleware constructor.
     *
     * @param EntityFactoryConfig $config
     */
    public function __construct(EntityFactoryConfig $config);

    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next): ResponseInterface;
}

