<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 20/04/17
 * Time: 09:39
 */

namespace Eukles\Slim\Handlers;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Handlers\AbstractError;

class ActionError extends AbstractError implements ActionErrorInterface
{
    
    use ApiProblemRendererTrait;
    
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
    ) {
    
        $this->writeToErrorLog($exception);
        
        return $this->render(
            $request,
            $response,
            "Failed to process action",
            $exception->getCode() ?: 500,
            $exception->getMessage() ?: "Unknown error",
            $this->displayErrorDetails ? get_class($exception) : "about:blank",
            $this->displayErrorDetails ? $exception->getTraceAsString() : ""
        );
    }
}
