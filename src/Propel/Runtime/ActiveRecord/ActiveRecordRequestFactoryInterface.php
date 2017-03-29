<?php
namespace Eukles\Propel\Runtime\ActiveRecord;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface ActiveRecordRequestFactoryInterface
 *
 * @package Core\Model
 */
interface ActiveRecordRequestFactoryInterface
{
    
    /**
     * Create a new instance of activeRecord and add it to Request attributes
     *
     * @param ActiveRecordRequestInterface $activeRecordRequest
     * @param ServerRequestInterface       $request
     * @param ResponseInterface            $response
     * @param callable                     $next
     * @param                              $nameOfParameterToAdd
     *
     * @return ResponseInterface
     */
    public function create(
        ActiveRecordRequestInterface $activeRecordRequest,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next,
        $nameOfParameterToAdd = null
    );
    
    /**
     * Fetch an existing instance of activeRecord and add it to Request attributes
     *
     * @param ActiveRecordRequestInterface $activeRecordRequest
     * @param ServerRequestInterface       $request
     * @param ResponseInterface            $response
     * @param callable                     $next
     * @param                              $nameOfParameterToAdd
     *
     * @return ResponseInterface
     */
    public function fetch(
        ActiveRecordRequestInterface $activeRecordRequest,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next,
        $nameOfParameterToAdd = null
    );
}
