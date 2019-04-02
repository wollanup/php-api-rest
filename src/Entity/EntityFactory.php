<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 17/10/16
 * Time: 16:45
 */

namespace Eukles\Entity;

use Eukles\Action\ActionInterface;
use Eukles\Container\ContainerInterface;
use Eukles\Container\ContainerTrait;
use Eukles\Util\PksFinder;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\PropelException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;

class EntityFactory implements EntityFactoryInterface
{

    use ContainerTrait;

    /**
     * EntityFactoryInterface constructor.
     *
     * @param ContainerInterface $c
     */
    public function __construct(ContainerInterface $c)
    {
        $this->container = $c;
    }

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
    ): ResponseInterface {
        $entityRequest = $config->createEntityRequest($request, $this->container);

        # make a new empty record
        $obj = $entityRequest->instantiateActiveRecord();

        # Execute beforeCreate hook, which can alter record
        $entityRequest->beforeCreate($obj, $request);

        # Then, alter object with allowed properties
        if ($config->isHydrateEntityFromRequest()) {
            $requestParams = $request->getQueryParams();
            $postParams    = $request->getParsedBody();
            if ($postParams) {
                $requestParams = array_merge($requestParams, (array)$postParams);
            }

            /** @noinspection PhpUndefinedMethodInspection */
            $obj->fromArray($entityRequest->getAllowedDataFromRequest($requestParams, $request->getMethod()));
        }

        # Execute afterCreate hook, which can alter record
        $entityRequest->afterCreate($obj, $request);

        $request = $request->withAttribute($config->getParameterToInjectInto(), $obj);
        /** @var Response $response */
        return $next($request, $response);
    }

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
    ): ResponseInterface {
        $entityRequest = $config->createEntityRequest($request, $this->container);

        # First, we try to determine PK in request path (most common case)
        if (isset($request->getAttribute('routeInfo')[2][$config->getRequestParameterName()])) {
            $entityRequest->setPrimaryKey($request->getAttribute('routeInfo')[2][$config->getRequestParameterName()]);
        }

        # Next, we create the query (ModelCriteria), based on Action class (which can alter the query)
        $query = $this->getQueryFromActiveRecordRequest($entityRequest);

        # Execute beforeFetch hook, which can enforce primary key
        $query = $entityRequest->beforeFetch($query, $request);

        # Now get the primary key in its final form
        $pk = $entityRequest->getPrimaryKey();
        if (null === $pk) {
            $handler = $entityRequest->getContainer()->getEntityRequestErrorHandler();

            return $handler->primaryKeyNotFound($entityRequest, $request,
                $response);
        }

        # Then, fetch object
        $obj = $query->findPk($pk);

        if ($obj === null) {
            $handler = $entityRequest->getContainer()->getEntityRequestErrorHandler();

            return $handler->entityNotFound($entityRequest, $request, $response);
        }

        # Get request params
        if ($config->isHydrateEntityFromRequest()) {
            $params     = $request->getQueryParams();
            $postParams = $request->getParsedBody();
            if ($postParams) {
                $params = array_merge($params, (array)$postParams);
            }

            # Then, alter object with allowed properties
            $obj->fromArray($entityRequest->getAllowedDataFromRequest($params, $request->getMethod()));
        }

        # Then, execute afterFetch hook, which can alter the object
        $entityRequest->afterFetch($obj, $request);

        $request = $request->withAttribute($config->getParameterToInjectInto(), $obj);

        return $next($request, $response);
    }

    /**
     * Fetch an existing collection of activeRecords and add it to Request attributes
     *
     * @param EntityRequestInterface $entityRequest
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @param                              $nameOfParameterToAdd
     *
     * @return ResponseInterface
     * @throws PropelException
     */
    public function fetchCollection(
        EntityRequestInterface $entityRequest,
        ServerRequestInterface $request,
        ResponseInterface $response,
        callable $next,
        $nameOfParameterToAdd = null
    ): ResponseInterface {

        $pks = [];
        if ($request->getMethod() === 'GET') {
            # GET : Try to find PKs in query
            $params = $request->getQueryParams();
            if (array_key_exists('id', $params)) {
                $pks = $params['id'];
            }
        } else {
            # POST/PATCH : Try to find PKs in body
            if (is_array($request->getParsedBody())) {
                $finder = new PksFinder(['id']);
                $pks    = $finder->find($request->getParsedBody());
            }
        }

        # Next, we create the query (ModelCriteria), based on Action class (which can alter the query)
        $query = $this->getQueryFromActiveRecordRequest($entityRequest);

        if (empty($pks)) {
            $handler = $entityRequest->getContainer()
                ->getEntityRequestErrorHandler();

            return $handler->primaryKeyNotFound($entityRequest, $request,
                $response);
        }

        # Then, fetch object
        $col = $query->findPks($pks);

        if ($col === null) {
            $handler = $entityRequest->getContainer()
                ->getEntityRequestErrorHandler();

            return $handler->entityNotFound($entityRequest, $request,
                $response);
        }

        # Finally, build name of parameter to inject in action method, will be used later
        if ($nameOfParameterToAdd === null) {
            $nameOfParameterToAdd
                = $entityRequest->getNameOfParameterToAdd(true);
        }
        $request = $request->withAttribute($nameOfParameterToAdd, $col);

        return $next($request, $response);
    }

    /**
     * Create the query (ModelCriteria), based on Action class (which can alter the query)
     *
     * @param EntityRequestInterface $activeRecordRequest
     *
     * @return ModelCriteria
     */
    private function getQueryFromActiveRecordRequest(
        EntityRequestInterface $activeRecordRequest
    ) {
        $actionClass = $activeRecordRequest->getActionClassName();
        /** @var ActionInterface $action */
        $action = $actionClass::create($activeRecordRequest->getContainer());

        return $action->createQuery();
    }
}
