<?php

namespace Eukles\Action;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ActionAbstract
 *
 * @package Eukles\Action
 */
abstract class ActionAbstract implements ActionInterface
{
    
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var ServerRequestInterface
     */
    protected $request;
    /**
     * @var ModelCriteria null
     */
    protected $requestQuery = null;
    /**
     * @var ResponseInterface
     */
    protected $response;
    
    /**
     * SlimControllerInterface constructor.
     *
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
        if ($c->has('request')) {
            $this->request = $c['request'];
        }
        if ($c->has('response')) {
            $this->response = $c['response'];
        }
    }
    
    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
    
    /**
     * @return ServerRequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    /**
     * @param ServerRequestInterface $serverRequest
     *
     * @return ActionInterface
     */
    public function setRequest(ServerRequestInterface $serverRequest)
    {
        $this->request = $serverRequest;
        
        return $this;
    }
    
    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }
    
    /**
     * @param ResponseInterface $response
     *
     * @return ActionInterface
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        
        return $this;
    }
}
