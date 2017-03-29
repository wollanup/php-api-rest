<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 17/10/16
 * Time: 16:45
 */

namespace Eukles\Propel\Runtime\ActiveRecord;

use Eukles\Action\ActionInterface;
use Propel\Runtime\Exception\EntityNotFoundException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ActiveRecordRequestFactory implements ActiveRecordRequestFactoryInterface
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
    ) {
        # make a new empty record
        $obj = $activeRecordRequest->instantiateActiveRecord();
        
        # Execute beforeCreate hook, which can alter record
        $activeRecordRequest->beforeCreate($obj);
        
        # Then, alter object with allowed properties
        /** @noinspection PhpUndefinedMethodInspection */
        $obj->fromArray(
            $activeRecordRequest->getAllowedDataFromRequest(
                $request->getParams(),
                $request->getMethod()
            ));
        
        # Execute afterCreate hook, which can alter record
        $activeRecordRequest->afterCreate($obj);
        
        # Finally, build name of parameter to inject in action method, will be used later
        if ($nameOfParameterToAdd === null) {
            $nameOfParameterToAdd = $activeRecordRequest->buildNameOfParameterToAdd();
        }
        /** @var $request ServerRequestInterface */
        $newRequest = $request->withAttribute($nameOfParameterToAdd, $obj);
        $response   = $next($newRequest, $response);
        
        return $response;
    }
    
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
     * @throws EntityNotFoundException
     */
    public function fetch(
        ActiveRecordRequestInterface $activeRecordRequest,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next,
        $nameOfParameterToAdd = null
    ) {
        
        # First, we try to determine PK in  request path (most common case)
        if (isset($request->getAttribute('routeInfo')[2]['id'])) {
            $activeRecordRequest->setPrimaryKey($request->getAttribute('routeInfo')[2]['id']);
        }
        
        # Next, we create the query (ModelCriteria), based on Action class (which can alter the query)
        $query = $this->getQueryFromActiveRecordRequest($activeRecordRequest);
        
        # Execute beforeFetch hook, which can enforce primary key
        $query = $activeRecordRequest->beforeFetch($query);
        
        # Now get the primary key in its final form
        $pk = $activeRecordRequest->getPrimaryKey();
        if (empty($pk)) {
            throw new \InvalidArgumentException('Primary key not set in beforeFetch() and not found in URI path');
        }
        
        # Then, fetch object
        $obj = $query->findPk($pk);
        
        if ($obj === null) {
            throw new EntityNotFoundException("Cannot fetch model from DB");
        }
        
        # Get request params
        $params     = $request->getQueryParams();
        $postParams = $request->getParsedBody();
        if ($postParams) {
            $params = array_merge($params, (array)$postParams);
        }
        
        # Then, alter object with allowed properties
        $obj->fromArray($activeRecordRequest->getAllowedDataFromRequest($params, $request->getMethod()));
        
        # Then, execute afterFetch hook, which can alter the object
        $activeRecordRequest->afterFetch($obj);
        
        # Finally, build name of parameter to inject in action method, will be used later
        if ($nameOfParameterToAdd === null) {
            $nameOfParameterToAdd = $activeRecordRequest->buildNameOfParameterToAdd();
        }
        $newRequest = $request->withAttribute($nameOfParameterToAdd, $obj);
        $response   = $next($newRequest, $response);
        
        return $response;
    }
    
    /**
     * Create the query (ModelCriteria), based on Action class (which can alter the query)
     *
     * @param ActiveRecordRequestInterface $activeRecordRequest
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     */
    private function getQueryFromActiveRecordRequest(ActiveRecordRequestInterface $activeRecordRequest)
    {
        $actionClassName = $activeRecordRequest->getActionClassName();
        /** @var ActionInterface $action */
        $action = $actionClassName::create($activeRecordRequest->getContainer());
        
        return $action->createQuery();
    }
}
