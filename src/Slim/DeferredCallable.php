<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/10/17
 * Time: 16:58
 */

namespace Eukles\Slim;

use Eukles\Container\ContainerInterface;
use Eukles\Container\ContainerTrait;
use Eukles\Service\Router\Route;
use Slim\CallableResolverAwareTrait;
use Slim\Interfaces\RouteInterface;

class DeferredCallable
{

    use CallableResolverAwareTrait;
    use ContainerTrait;
    /**
     * @var callable|string
     */
    private $callable;

    /**
     * DeferredMiddleware constructor.
     *
     * @param callable|string    $callable
     * @param ContainerInterface $container
     */
    public function __construct($callable, ContainerInterface $container = null)
    {
        $this->callable  = $callable;
        $this->container = $container;
    }

    public function __invoke()
    {
        $args = func_get_args();

        $callable = $this->resolveCallable($this->callable);
        if ($callable instanceof \Closure) {
            $callable = $callable->bindTo($this->container);
        }

        if(is_array($callable) && array_key_exists(0, $callable) && is_object($callable[0])){
            if(method_exists($callable[0], 'setContainer')){
                $callable[0]->setContainer($this->getContainer());
            }
        }
        elseif(is_object($callable)){
            if(method_exists($callable, 'setContainer')){
                $callable->setContainer($this->getContainer());
            }
        }


        return call_user_func_array($callable, $args);
    }

    /**
     * @return callable|string
     */
    public function getCallable()
    {
        return $this->callable;
    }
}
