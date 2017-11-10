<?php

namespace Eukles\Action;

use Eukles\Container\ContainerTrait;
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

    use ContainerTrait;
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
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * @param ServerRequestInterface $serverRequest
     *
     * @return ActionInterface
     */
    public function setRequest(ServerRequestInterface $serverRequest
    ): ActionInterface {
        $this->request = $serverRequest;

        return $this;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return ActionInterface
     */
    public function setResponse(ResponseInterface $response): ActionInterface
    {
        $this->response = $response;

        return $this;
    }
}
