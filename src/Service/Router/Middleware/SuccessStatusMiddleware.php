<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/10/17
 * Time: 16:24
 */

namespace Eukles\Service\Router\Middleware;

use Psr\Http\Message\ResponseInterface;
use Slim\Http\Response;

/**
 * Class EntityFetch
 *
 * @package Eukles\Entity\Middleware
 */
class SuccessStatusMiddleware
{

    /**
     * @var int
     */
    protected $status;

    /**
     * SuccessStatusMiddleware constructor.
     *
     * @param int $status
     */
    public function __construct(int $status)
    {
        $this->status = $status;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return ResponseInterface
     */
    public function __invoke($request, $response, $next): ResponseInterface
    {
        /** @var Response $response */
        $response = $next($request, $response);
        if ($response->isSuccessful()) {
            $response = $response->withStatus($this->status);
        }

        return $response;
    }
}
