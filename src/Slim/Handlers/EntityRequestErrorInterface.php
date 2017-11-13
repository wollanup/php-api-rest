<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 20/04/17
 * Time: 09:57
 */

namespace Eukles\Slim\Handlers;

use Eukles\Entity\EntityRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface EntityRequestErrorInterface
{

    /**
     * @param EntityRequestInterface $entityRequest
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function entityNotFound(
        EntityRequestInterface $entityRequest,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface;

    /**
     * @param EntityRequestInterface $entityRequest
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function primaryKeyNotFound(
        EntityRequestInterface $entityRequest,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface;
}
