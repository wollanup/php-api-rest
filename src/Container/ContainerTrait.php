<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/10/17
 * Time: 15:14
 */

namespace Eukles\Container;

trait ContainerTrait
{
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * Allows serialization of RouteMap classes
     *
     * @return array
     */
    public function __sleep(): array
    {
        $properties = get_object_vars($this);
        unset($properties['container']);
        $properties = array_keys($properties);
        
        return $properties;
    }
    
    /**
     * @return \Psr\Container\ContainerInterface
     */
    public function getContainer(): \Psr\Container\ContainerInterface
    {
        return $this->container;
    }
    
    /**
     * @param \Psr\Container\ContainerInterface $c
     *
     * @return $this
     */
    public function setContainer(\Psr\Container\ContainerInterface $c)
    {
        $this->container = $c;
        
        return $this;
    }
}
