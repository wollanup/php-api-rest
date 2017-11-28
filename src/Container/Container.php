<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 18/05/16
 * Time: 14:45
 */

namespace Eukles\Container;

use Eukles\Config\Config;
use Eukles\Config\ConfigInterface;
use Eukles\Entity\EntityFactory;
use Eukles\Entity\EntityFactoryInterface;
use Eukles\Service\Request\Pagination\RequestPagination;
use Eukles\Service\Request\Pagination\RequestPaginationInterface;
use Eukles\Service\Request\QueryModifier\RequestQueryModifier;
use Eukles\Service\Request\QueryModifier\RequestQueryModifierInterface;
use Eukles\Service\ResponseBuilder\ResponseBuilderInterface;
use Eukles\Service\ResponseFormatter\ResponseFormatterInterface;
use Eukles\Service\Router\RouterInterface;
use Eukles\Service\RoutesClasses\Exception\RoutesClassesServiceMissingException;
use Eukles\Service\RoutesClasses\RoutesClassesInterface;
use Eukles\Slim\Handlers\ActionError;
use Eukles\Slim\Handlers\ActionErrorInterface;
use Eukles\Slim\Handlers\EntityRequestError;
use Eukles\Slim\Handlers\EntityRequestErrorInterface;
use Eukles\Slim\Handlers\Strategies\ActionStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Container as SlimContainer;
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Class Container
 *
 * @property-read Request request
 *
 * @package Eukles\Service
 */
class Container extends SlimContainer implements ContainerInterface
{

    /**
     * Container constructor.
     *
     * @param array $values
     *
     * @throws RoutesClassesServiceMissingException
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        /**
         * Config Service
         *
         * Must be very first service added to Container, other services can use it in their creation
         *
         * @return Config
         */
        $this['config'] = function () use ($values) {
            return new Config($this['settings']->all());
        };

        # Default Found Handler
        if (!isset($values[self::HANDLER])) {
            $this[self::HANDLER] = function (ContainerInterface $c) {
                return new ActionStrategy($c);
            };
        }

        # Default Request Query Modifier (Do nothing),
        # Use your own implementation of RequestQueryModifierInterface
        if (!isset($values[self::ENTITY_FACTORY])) {
            $this[self::ENTITY_FACTORY] = function () {
                return new EntityFactory();
            };
        }

        # Default Request Query Modifier,
        # You can use your own implementation of RequestQueryModifierInterface
        if (!isset($values[self::REQUEST_PAGINATION])) {
            $this[self::REQUEST_PAGINATION] = function (ContainerInterface $c) {
                return new RequestPagination($c->getRequest());
            };
        }

        # Default Request Query Modifier (Do nothing),
        # Use your own implementation of RequestQueryModifierInterface
        if (!isset($values[self::REQUEST_QUERY_MODIFIER])) {
            $this[self::REQUEST_QUERY_MODIFIER] = function (ContainerInterface $c) {
                return new RequestQueryModifier($c->getRequest());
            };
        }

        # Default Response Builder (Do nothing),
        # Use your own implementation of RequestQueryModifierInterface
        if (!isset($values[self::RESPONSE_BUILDER])) {
            $this[self::RESPONSE_BUILDER] = function () {
                return function ($result) {
                    return $result;
                };
            };
        }

        # Default Response Formatter (Do nothing),
        # Use your own implementation of RequestQueryModifierInterface
        if (!isset($values[self::RESPONSE_FORMATTER])) {
            $this[self::RESPONSE_FORMATTER] = function () {
                return function (Response $response, $result) {
                    if ($response->getBody()->isWritable()) {
                        if (is_array($result)) {
                            return $response->withJson($result);
                        }
                        if (is_scalar($result) && !empty($result)) {
                            return $response->getBody()->write(is_array($result) ? json_encode($result) : $result);
                        }
                    }

                    return $response;
                };
            };
        }

        # Default error handler, you can use your own implementation
        if (!isset($values[self::ENTITY_REQUEST_ERROR_HANDLER])) {
            $this[self::ENTITY_REQUEST_ERROR_HANDLER] = function () {
                return new EntityRequestError();
            };
        }

        # Default error handler, you can use your own implementation
        if (!isset($values[self::ACTION_ERROR_HANDLER])) {
            $this[self::ACTION_ERROR_HANDLER] = function () {
                return new ActionError();
            };
        }
    }

    /**
     * @return ActionErrorInterface
     */
    public function getActionErrorHandler(): ActionErrorInterface
    {
        return $this[self::ACTION_ERROR_HANDLER];
    }

    /**
     * @return ConfigInterface
     */
    public function getConfig(): ConfigInterface
    {
        return $this['config'];
    }

    /**
     * @return EntityFactoryInterface
     */
    public function getEntityFactory(): EntityFactoryInterface
    {
        return $this[self::ENTITY_FACTORY];
    }

    /**
     * @return EntityRequestErrorInterface
     */
    public function getEntityRequestErrorHandler(): EntityRequestErrorInterface
    {
        return $this[self::ENTITY_REQUEST_ERROR_HANDLER];
    }

    /**
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this['request'];
    }

    /**
     * @return RequestPaginationInterface
     */
    public function getRequestPagination(): RequestPaginationInterface
    {
        return $this[self::REQUEST_PAGINATION];
    }

    /**
     * @return RequestQueryModifierInterface
     */
    public function getRequestQueryModifier(): RequestQueryModifierInterface
    {
        return $this[self::REQUEST_QUERY_MODIFIER];
    }

    /**
     * @return ResponseBuilderInterface
     */
    public function getResponseBuilder(): ResponseBuilderInterface
    {
        return $this[self::RESPONSE_BUILDER];
    }

    /**
     * @return ResponseFormatterInterface
     */
    public function getResponseFormatter(): ResponseFormatterInterface
    {
        return $this[self::RESPONSE_FORMATTER];
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this['response'];
    }

    /**
     * @return RouterInterface
     */
    public function getRouter(): RouterInterface
    {
        return $this[self::ROUTER];
    }

    /**
     * @return RoutesClassesInterface
     */
    public function getRoutesClasses(): RoutesClassesInterface
    {
        return $this[self::ROUTES_CLASSES];
    }
}
