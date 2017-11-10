<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 20/04/17
 * Time: 09:57
 */

namespace Eukles\Slim\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ActionErrorInterface
{

    /**
     * @param \Exception             $exception
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     *
     * @return ResponseInterface
     */
    public function __invoke(
        \Exception $exception,
        ServerRequestInterface $request,
        ResponseInterface $response
    ): ResponseInterface;
}
