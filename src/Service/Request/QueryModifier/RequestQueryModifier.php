<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 26/07/16
 * Time: 11:02
 */

namespace Eukles\Service\Request\QueryModifier;

use Eukles\Service\QueryModifier\QueryModifierInterface;
use Eukles\Service\Request\QueryModifier\Modifier\EasyFilter;
use Eukles\Service\Request\QueryModifier\Modifier\FilterModifier;
use Eukles\Service\Request\QueryModifier\Modifier\SortModifier;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class RequestQueryModifier
 *
 * @package Ged\Service
 */
class RequestQueryModifier implements RequestQueryModifierInterface
{

    /**
     * @var array
     */
    protected $excludedForEasyFilters = ["sort", "filter", "limit", "page"];
    /**
     * @var ModelCriteria
     */
    protected $query;
    /**
     * @var ServerRequestInterface
     */
    protected $request;

    /**
     * Session constructor.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     *
     * @return \Propel\Runtime\ActiveQuery\ModelCriteria
     * @throws \Eukles\Service\QueryModifier\UseQuery\UseQueryFromDotNotationException
     */
    public function apply(ModelCriteria $query)
    {
        # Merge queries
        if ($this->query) {
            $query->mergeWith($this->query);
        }

        if (strtoupper($this->request->getMethod()) !== 'GET') {
            return $query;
        }

        # Apply filters
        $filters = new FilterModifier($this->request);
        $filters->apply($query);

        # Apply sorters
        $sorters = new SortModifier($this->request);
        $sorters->apply($query);

        $easySorters = new EasyFilter($this->request, $this->excludedForEasyFilters);
        $easySorters->apply($query);

        return $query;
    }

    /**
     * @return array
     */
    public function getExcludedForEasyFilters(): array
    {
        return $this->excludedForEasyFilters;
    }

    /**
     * @param array $excludedForEasyFilters
     *
     * @return RequestQueryModifier
     */
    public function setExcludedForEasyFilters(array $excludedForEasyFilters): RequestQueryModifier
    {
        $this->excludedForEasyFilters = $excludedForEasyFilters;

        return $this;
    }

    /**
     * @param array $excludedForEasyFilters
     *
     * @return RequestQueryModifier
     */
    public function addExcludedForEasyFilters(array $excludedForEasyFilters): RequestQueryModifier
    {
        $this->excludedForEasyFilters = array_merge($this->excludedForEasyFilters, $excludedForEasyFilters);

        return $this;
    }

    /**
     * @param ModelCriteria $query
     *
     * @return QueryModifierInterface
     */
    public function setQuery(ModelCriteria $query): QueryModifierInterface
    {
        $this->query = $query;

        return $this;
    }
}
