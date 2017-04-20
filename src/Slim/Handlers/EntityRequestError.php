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

class EntityRequestError implements EntityRequestErrorInterface
{
    
    use ApiProblemRendererTrait;
    
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
    ) {
        $model = $entityRequest->getTableMap()->getPhpName();
        $pk    = $entityRequest->getPrimaryKey();
        
        return $this->render(
            $request,
            $response,
            "Entity Not Found",
            404,
            "{$model} #{$pk} not found"
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
