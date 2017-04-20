<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 20/04/17
 * Time: 10:29
 */

namespace Eukles\Slim\Handlers;

use Crell\ApiProblem\ApiProblem;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use RKA\ContentTypeRenderer\ApiProblemRenderer;

trait ApiProblemRendererTrait
{
    
    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param string                 $title
     * @param int                    $status
     * @param string                 $detail
     * @param string                 $instance
     *
     * @return ResponseInterface
     */
    public function render(
        ServerRequestInterface $request,
        ResponseInterface $response,
        $title = "Unknown Error",
        $status = 500,
        $detail = "",
        $instance = ""
    ) {
        $problem = new ApiProblem($title);
        $problem->setStatus($status);
        $problem->setDetail($detail);
        $problem->setInstance($instance);
        $renderer = new ApiProblemRenderer();
        
        return $renderer->render($request, $response, $problem);
    }
}
