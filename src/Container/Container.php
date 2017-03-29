<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 18/05/16
 * Time: 14:45
 */

namespace Eukles\Container;

use Eukles\Entity\EntityFactory;
use Eukles\Entity\EntityFactoryInterface;
use Eukles\Service\QueryModifier\RequestQueryModifierInterface;
use Eukles\Service\Request\QueryModifier\RequestQueryModifier;
use Eukles\Service\ResponseBuilder\ResponseBuilderInterface;
use Eukles\Service\ResponseFormatter\ResponseFormatterInterface;
use Eukles\Service\Router\RouterInterface;
use Eukles\Service\RoutesClasses\Exception\RoutesClassesServiceMissingException;
use Eukles\Service\RoutesClasses\RoutesClassesInterface;
use Eukles\Slim\Handlers\Strategies\ActionStrategy;
use Psr\Http\Message\ResponseInterface;
use Slim\Container as SlimContainer;
use Slim\Http\Request;

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
        
        # Default Found Handler
        if (!isset($values[self::HANDLER])) {
            $this[self::HANDLER] = function (ContainerInterface $c) {
                return new ActionStrategy($c);
            };
        }
        
        # Default Request Query Modifier (Do nothing),
        # Use your own implementation of RequestQueryModifierInterface
        if (!isset($values[self::ENTITY_FACTORY])) {
            $this[self::ENTITY_FACTORY] = function (ContainerInterface $c) {
                return new EntityFactory($c);
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
                return function (ResponseInterface $response, $result) {
                    if ($response->getBody()->isWritable()) {
                        $response->getBody()
                            ->write(is_array($result) ? json_encode($result) : $result);
                    }
                    
                    return $response;
                };
            };
        }
    }
    
    /**
     * @return EntityFactoryInterface
     */
    public function getEntityFactory()
    {
        return $this[self::ENTITY_FACTORY];
    }
    
    /**
     * @return Request
     */
    public function getRequest()
    {
        return $this['request'];
    }
    
    /**
     * @return RequestQueryModifierInterface
     */
    public function getRequestQueryModifier()
    {
        return $this[self::REQUEST_QUERY_MODIFIER];
    }
    
    /**
     * @return ResponseBuilderInterface
     */
    public function getResponseBuilder()
    {
        return $this[self::RESPONSE_BUILDER];
    }
    
    /**
     * @return ResponseFormatterInterface
     */
    public function getResponseFormatter()
    {
        return $this[self::RESPONSE_FORMATTER];
    }
    
    /**
     * @return RouterInterface
     */
    public function getRouter()
    {
        return $this[self::ROUTER];
    }
    
    /**
     * @return RoutesClassesInterface
     */
    public function getRoutesClasses()
    {
        return $this[self::ROUTES_CLASSES];
    }
}
