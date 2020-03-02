<?php

namespace Eukles\Entity;

use Eukles\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface EntityFactoryInterface
 *
 * @package Core\Model
 */
interface EntityFactoryInterface
{

    /**
     * EntityFactoryInterface constructor.
     *
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c);

    /**
     * Create a new instance of activeRecord and add it to Request attributes
     *
     * @param EntityFactoryConfig    $config
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function create(
        EntityFactoryConfig $config,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface;

    /**
     * Fetch an existing instance of activeRecord and add it to Request attributes
     *
     * @param EntityFactoryConfig    $config
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param callable               $next
     *
     * @return ResponseInterface
     */
    public function fetch(
        EntityFactoryConfig $config,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface;

    /**
     * Fetch an existing collection of activeRecords and add it to Request attributes
     *
     * @param EntityFactoryConfig $config
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function fetchCollection(
        EntityFactoryConfig $config,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next
    ): ResponseInterface;
}
