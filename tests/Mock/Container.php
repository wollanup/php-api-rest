<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 27/03/17
 * Time: 09:47
 */

namespace Test\Eukles\Mock;

use Eukles\Config\ConfigInterface;
use Eukles\Container\ContainerInterface;
use Eukles\Entity\EntityFactoryInterface;
use Eukles\Service\Request\Pagination\RequestPaginationInterface;
use Eukles\Service\Request\QueryModifier\RequestQueryModifierInterface;
use Eukles\Service\ResponseBuilder\ResponseBuilderInterface;
use Eukles\Service\ResponseFormatter\ResponseFormatterInterface;
use Eukles\Service\Router\RouterInterface;
use Eukles\Service\RoutesClasses\RoutesClassesInterface;
use Eukles\Slim\Handlers\ActionErrorInterface;
use Eukles\Slim\Handlers\EntityRequestErrorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Container implements ContainerInterface
{

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerExceptionInterface Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        // TODO: Implement get() method.
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        // TODO: Implement has() method.
    }

    /**
     * @return ActionErrorInterface
     */
    public function getActionErrorHandler(): ActionErrorInterface
    {
        // TODO: Implement getActionErrorHandler() method.
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        // TODO: Implement getConfig() method.
    }

    /**
     * @return EntityFactoryInterface
     */
    public function getEntityFactory(): EntityFactoryInterface
    {
        // TODO: Implement getEntityFactory() method.
    }

    /**
     * @return EntityRequestErrorInterface
     */
    public function getEntityRequestErrorHandler(): EntityRequestErrorInterface
    {
        // TODO: Implement getEntityRequestErrorHandler() method.
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        // TODO: Implement getRequest() method.
    }

    /**
     * @return RequestPaginationInterface
     */
    public function getRequestPagination(): RequestPaginationInterface
    {
        // TODO: Implement getRequestPagination() method.
    }

    /**
     * @return RequestQueryModifierInterface
     */
    public function getRequestQueryModifier(): RequestQueryModifierInterface
    {
        // TODO: Implement getRequestQueryModifier() method.
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        // TODO: Implement getResponse() method.
    }

    /**
     * @return ResponseBuilderInterface
     */
    public function getResponseBuilder(): ResponseBuilderInterface
    {
        // TODO: Implement getResponseBuilder() method.
    }

    /**
     * @return ResponseFormatterInterface
     */
    public function getResponseFormatter(): ResponseFormatterInterface
    {
        // TODO: Implement getResponseFormatter() method.
    }

    /**
     * @return RouterInterface
     */
    public function getRouter(): RouterInterface
    {
        // TODO: Implement getRouter() method.
    }

    /**
     * @return RoutesClassesInterface
     */
    public function getRoutesClasses(): RoutesClassesInterface
    {
        // TODO: Implement getRoutesClasses() method.
    }
}
