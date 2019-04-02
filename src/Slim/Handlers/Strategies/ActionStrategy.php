<?php

namespace Eukles\Slim\Handlers\Strategies;

use Closure;
use Eukles\Action;
use Eukles\Container\ContainerInterface;
use Eukles\Service\Pagination\PaginationInterface;
use Eukles\Service\QueryModifier\QueryModifierInterface;
use Eukles\Service\ResponseBuilder\ResponseBuilderException;
use Eukles\Service\ResponseFormatter\ResponseFormatterException;
use Eukles\Slim\Handlers\ApiProblemRendererTrait;
use Exception;
use InvalidArgumentException;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UploadedFileInterface;
use ReflectionClass;
use ReflectionException;
use ReflectionParameter;
use Slim\Interfaces\InvocationStrategyInterface;

/**
 * Class RequestResponseHandler
 *
 * @package Eukles\Slim\Handlers\Strategies
 */
class ActionStrategy implements InvocationStrategyInterface
{

    use ApiProblemRendererTrait;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * ActionStrategy constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Invoke a route callable with request, response and all route parameters
     * as individual arguments.
     *
     * @param array|callable         $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $routeArguments
     *
     * @return mixed
     * @throws Exception
     */
    public function __invoke(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {

        return $this->callHandler($callable, $request, $response, $routeArguments);
    }

    /**
     * Call a method in an Action class
     *
     * May be overriden to add some logic before or after call
     *
     * @param callable               $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $routeArguments
     *
     * @return mixed
     */
    public function callAction(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {

        try {
            return call_user_func_array($callable, $this->buildParams($callable, $request, $routeArguments));
        } catch (Exception $e) {
            $handler = $this->container->getActionErrorHandler();

            return $handler($e, $request, $response);
        }
    }

    /**
     * Build list of parameters needed by Action::method
     *
     * @param callable|array                 $callable
     * @param ServerRequestInterface         $request
     * @param                                $routeArguments
     *
     * @return array
     * @throws ReflectionException
     */
    protected function buildParams(
        callable $callable,
        ServerRequestInterface $request,
        $routeArguments
    ) {

        if (is_array($callable) === false) {
            return [];
        }

        $r = new ReflectionClass($callable[0]);
        $m            = $r->getMethod($callable[1]);
        $paramsMethod = $m->getParameters();

        if (empty($paramsMethod)) {
            return [];
        }

        $requestParams = $request->getQueryParams();
        $postParams    = $request->getParsedBody();
        if ($postParams) {
            $requestParams = array_merge($requestParams, (array)$postParams);
        }

        $buildParams   = [];

        /** @var ReflectionParameter[] $params */
        foreach ($paramsMethod as $param) {
            $name  = $param->getName();
            $class = $param->getClass();
            if (null !== $class) {
                if (($p = $request->getAttribute($name)) !== null) {
                    $buildParams[] = $p;
                } elseif ($class->implementsInterface(QueryModifierInterface::class)) {
                    $buildParams[] = $this->container->getRequestQueryModifier();
                } elseif ($class->implementsInterface(PaginationInterface::class)) {
                    $buildParams[] = $this->container->getRequestPagination();
                } elseif ($class->implementsInterface(UploadedFileInterface::class)) {
                    $files = $request->getUploadedFiles();
                    $files = array_values($files);
                    /** @var UploadedFileInterface $attachment */
                    $buildParams[] = isset($files[0]) ? $files[0] : null;
                } elseif (!$param->isDefaultValueAvailable()) {
                    throw new InvalidArgumentException(
                        "Missing or null required parameter '{$name}' in " . $r->getName() . "::" . $m->getName()
                    );
                }
            } else {
                if (isset($routeArguments[$name])) {
                    $paramValue = $routeArguments[$name];
                } elseif (isset($requestParams[$name])) {
                    $paramValue = $requestParams[$name];
                } elseif (($p = $request->getAttribute($name)) !== null) {
                    $paramValue = $p;
                } elseif ($param->isDefaultValueAvailable()) {
                    $paramValue = $param->getDefaultValue();
                } else {
                    throw new InvalidArgumentException(
                        "Missing or null required parameter '{$name}' in " . $r->getName() . "::" . $m->getName()
                    );
                }
                $buildParams[] = $paramValue;
            }
        }

        return $buildParams;
    }

    /**
     * Build a string response
     *
     * @param mixed                               $result
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     * @throws Exception
     */
    protected function buildResponse($result, ResponseInterface $response, RequestInterface $request)
    {

        $responseBuilder   = $this->container->getResponseBuilder();
        $responseFormatter = $this->container->getResponseFormatter();

        if (!is_callable($responseBuilder)) {
            throw new ResponseBuilderException('ResponseBuilder must be callable or implements ResponseBuilderInterface');
        }
        if (!is_callable($responseFormatter)) {
            throw new ResponseFormatterException('ResponseFormatter must be callable or implements ResponseFormatterInterface');
        }
        if (method_exists($responseBuilder, 'setRequest')) {
            $responseBuilder->setRequest($request);
        }

        $result = $responseBuilder($result);

        return $responseFormatter($response, $result);
    }

    /**
     * Call action with built params
     *
     * @param callable               $callable
     * @param ServerRequestInterface $request
     * @param ResponseInterface      $response
     * @param array                  $routeArguments
     *
     * @return mixed
     * @throws Exception
     */
    protected function callHandler(
        callable $callable,
        ServerRequestInterface $request,
        ResponseInterface $response,
        array $routeArguments
    ) {
        # Action is a closure
        if ($callable instanceof Closure) {
            array_unshift($routeArguments, $request, $response);
            $result = call_user_func_array($callable, $routeArguments);
        } else {
            # Action is a method of an Action class
            if (is_array($callable) && $callable[0] instanceof Action\ActionInterface) {
                $callable[0]->setResponse($response);
                $callable[0]->setRequest($request);
            }

            # Call Action method
            $result   = $this->callAction($callable, $request, $response, $routeArguments);
            /** @var Action\ActionInterface $action */
            $action   = $callable[0];
            $response = $action->getResponse();
        }

        if (($result instanceof ResponseInterface)) {
            $response = $result;
        } else {
            $response = $this->buildResponse($result, $response, $request);
        }

        return $response;
    }
}
