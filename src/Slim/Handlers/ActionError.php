<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 20/04/17
 * Time: 09:39
 */

namespace Eukles\Slim\Handlers;

use Eukles\Entity\EntityRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActionError implements ActionErrorInterface
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
        return $this->render(
            $request,
            $response,
            "Failed to process action",
            $exception->getCode() ?: 500,
            $exception->getMessage() ?: "Unknown error"
        );
    }
    
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
    ) {
        $model = $entityRequest->getTableMap()->getPhpName();
        
        return $this->render(
            $request,
            $response,
            "Primary Key Not Found in request",
            404,
            "Primary key for {$model} not found in request"
        );
    }
}
