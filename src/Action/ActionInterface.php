<?php

namespace Eukles\Action;

use Eukles\Service\QueryModifier\QueryModifierInterface;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface ActionInterface
 *
 * @package Eukles\Propel\Runtime\ActiveRecord
 */
interface ActionInterface
{

    /**
     * ActionInterface constructor.
     *
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c);

    /**
     * @param QueryModifierInterface $qm
     *
     * @return ModelCriteria
     */
    public function createQuery(QueryModifierInterface $qm = null);

    /**
     * @return ContainerInterface
     */
    public function getContainer(): ContainerInterface;

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;

    /**
     * @param ResponseInterface $response
     *
     * @return ActionInterface
     */
    public function setResponse(ResponseInterface $response): ActionInterface;

    /**
     * @param RequestInterface $request
     *
     * @return ActionInterface
     */
    public function setRequest(RequestInterface $request): ActionInterface;

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * Action factory
     *
     * @param ContainerInterface $c
     *
     * @return ActionInterface
     */
    public static function create(ContainerInterface $c);
}
