<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 01/12/17
 * Time: 14:39
 */

namespace Eukles\Service\Request\QueryModifier\Modifier;

use Eukles\Service\QueryModifier\Easy\Builder\Filter;
use Eukles\Service\QueryModifier\Easy\Modifier;
use Eukles\Service\QueryModifier\UseQuery\UseQueryFromDotNotationException;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Http\Message\ServerRequestInterface;

class EasyFilter
{

    /**
     * @var array
     */
    protected $ignoredParams;
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * EasySorter constructor.
     *
     * @param ServerRequestInterface $request
     * @param array                  $ignoredParams
     */
    public function __construct(ServerRequestInterface $request, array $ignoredParams = [])
    {
        $this->ignoredParams = $ignoredParams;
        $this->request       = $request;
    }

    /**
     * @param ModelCriteria $query
     *
     * @throws UseQueryFromDotNotationException
     */
    public function apply(ModelCriteria $query)
    {
        $modifier = new Modifier($query);
        $rawQueryString = $this->request->getUri()->getQuery();
        $decodedQueryString = urldecode($rawQueryString);
        $rawQueryParams = explode('&', $decodedQueryString);
        $queryParams = [];
        foreach ($rawQueryParams as $rawQueryParam) {
            if (empty($rawQueryParam)) {
                # Ignore empty param '&'
                continue;
            }
            list($key, $val) = explode('=', $rawQueryParam);
            if (empty($key)) {
                # Ignore empty key '=value'
                continue;
            }
            $queryParams[$key] = (string)$val;
        }

        foreach ($queryParams as $column => $value) {
            # Ignored params
            if (is_string($value) === false || in_array($column, $this->ignoredParams)) {
                continue;
            }
            $filter = new Filter($value);
            $modifier->filterBy($column, $filter->getValue(), $filter->getOperator());
        }
    }
}
