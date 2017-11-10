<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/10/17
 * Time: 16:24
 */

namespace Eukles\Entity\Middleware;

use Eukles\Container\ContainerInterface;
use Eukles\Container\ContainerTrait;
use Eukles\Entity\EntityFactoryConfig;
use Psr\Http\Message\ResponseInterface;

/**
 * Class EntityFetch
 * @method ContainerInterface getContainer()
 *
 * @package Eukles\Entity\Middleware
 */
class EntityFetch implements RouteEntityMiddlewareInterface
{

    use ContainerTrait;
    /**
     * @var EntityFactoryConfig
     */
    protected $config;

    /**
     * EntityFetch constructor.
     *
     * @param EntityFactoryConfig $config
     */
    public function __construct(EntityFactoryConfig $config)
    {
        $this->container = $config->getContainer();
        $this->config    = $config;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next): ResponseInterface
    {
        /** @var ContainerInterface $this */
        $response = $this->getContainer()->getEntityFactory()->fetch(
            $this->config,
            $request,
            $response,
            $next
        );

        return $response;
    }
}
