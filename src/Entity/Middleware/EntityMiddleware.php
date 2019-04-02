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
use RuntimeException;

/**
 * Class EntityFetch
 * @method ContainerInterface getContainer()
 *
 * @package Eukles\Entity\Middleware
 */
class EntityMiddleware implements RouteEntityMiddlewareInterface
{

    use ContainerTrait;
    /**
     * @var EntityFactoryConfig
     */
    protected $config;

    /**
     * EntityFetch constructor.
     *
     * @param ContainerInterface  $container
     * @param EntityFactoryConfig $config
     */
    public function __construct(ContainerInterface $container, EntityFactoryConfig $config)
    {
        $this->container = $container;
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
        if ($this->config->isTypeFetch()) {
            return $this->getContainer()->getEntityFactory()->fetch(
                $this->config,
                $request,
                $response,
                $next
            );
        } elseif ($this->config->isTypeCreate()) {
            return $this->getContainer()->getEntityFactory()->create(
                $this->config,
                $request,
                $response,
                $next
            );
        } else {
            throw new RuntimeException('Invalid EntityFactoryConfig type');
        }
    }
}
