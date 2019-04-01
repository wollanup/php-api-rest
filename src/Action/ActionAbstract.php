<?php

namespace Eukles\Action;

use Eukles\Container\ContainerTrait;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ActionAbstract
 *
 * @package Eukles\Action
 */
abstract class ActionAbstract implements ActionInterface
{

    use ContainerTrait;
    /**
     * @var ModelCriteria null
     */
    protected $requestQuery = null;
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * SlimControllerInterface constructor.
     *
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;

        if ($c->has('response')) {
            $this->response = $c['response'];
        }
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

    /**
     * @param RequestInterface $request
     *
     * @return ActionInterface
     */
    public function setRequest(RequestInterface $request): ActionInterface
    {
        $this->request = $request;

        return $this;
    }

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }
}
