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
use Slim\CallableResolverAwareTrait;

class DeferredCallable
{
    
    use CallableResolverAwareTrait;
    use ContainerTrait;
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
        $callable = $this->resolveCallable($this->callable);
        if ($callable instanceof \Closure) {
            $callable = $callable->bindTo($this->container);
        }
        
        $args = func_get_args();
        
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
