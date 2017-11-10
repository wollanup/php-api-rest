<?php

namespace Eukles\Action;

use Eukles\Service\QueryModifier\QueryModifierInterface;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

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
     * @return ServerRequestInterface
     */
    public function getRequest(): ServerRequestInterface;

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface;

    /**
     * @param ServerRequestInterface $serverRequest
     *
     * @return ActionInterface
     */
    public function setRequest(ServerRequestInterface $serverRequest
    ): ActionInterface;

    /**
     * @param ResponseInterface $response
     *
     * @return ActionInterface
     */
    public function setResponse(ResponseInterface $response): ActionInterface;

    /**
     * Action factory
     *
     * @param ContainerInterface $c
     *
     * @return ActionInterface
     */
    public static function create(ContainerInterface $c);
}
