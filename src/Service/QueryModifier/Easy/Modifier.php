<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 10/01/18
 * Time: 09:16
 */

namespace Eukles\Service\QueryModifier\Easy;

use Eukles\Service\QueryModifier\UseQuery\UseQueryFromDotNotation;
use Eukles\Service\QueryModifier\UseQuery\UseQueryFromDotNotationException;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Exception\BadMethodCallException;

class Modifier
{

    /**
     * @var UseQueryFromDotNotation
     */
    protected $dotUseQuery;
    /**
     * @var array List of failed filters
     */
    protected $failures = [];
    /**
     * @var ModelCriteria
     */
    protected $query;

    public function __construct(ModelCriteria $query)
    {
        $this->query = $query;
    }

    /**
     * @param $dotProperty
     * @param $value
     * @param $operator
     *
     * @return ModelCriteria
     * @throws UseQueryFromDotNotationException
     * @throws BadMethodCallException
     */
    public function filterBy(string $dotProperty, $value = null, $operator = null): ModelCriteria
    {

        $property    = $this->before($dotProperty);
        $method      = $this->buildMethodName(__FUNCTION__, $property);
        if ($this->methodExists($method)) {
            $this->query = $this->query->{$method}($value, $operator);
        } else {
            $this->failures[] = $method;
        }
        $this->after();

        return $this->query;
    }

    /**
     * Determine if method is callable in Query class
     *
     * @param $method
     * @return bool
     */
    public function methodExists($method)
    {
        return method_exists($this->query, $method);
    }

    /**
     * @param string $dotProperty
     * @param        $order
     *
     * @return ModelCriteria
     * @throws UseQueryFromDotNotationException
     */
    public function orderBy(string $dotProperty, $order = Criteria::ASC): ModelCriteria
    {
        $property    = $this->before($dotProperty);
        $this->query = $this->query->orderBy($property, $order);
        $this->after();

        return $this->query;
    }

    /**
     * List of failed method call
     *
     * @return array
     */
    public function getFailures(): array
    {
        return $this->failures;
    }

    /**
     * @throws UseQueryFromDotNotationException
     */
    private function after()
    {
        $this->query = $this->dotUseQuery->endUse();
    }

    /**
     * @param string $dotProperty
     *
     * @return string
     * @throws UseQueryFromDotNotationException
     */
    private function before(string $dotProperty)
    {
        $this->dotUseQuery = new UseQueryFromDotNotation($this->query, $dotProperty);
        $property          = $this->dotUseQuery->getProperty();
        $this->query       = $this->dotUseQuery->useQuery();

        return $property;
    }

    /**
     * @param string $action
     * @param string $property
     *
     * @return string
     */
    private function buildMethodName(string $action, string $property): string
    {
        return $action . $property;
    }
}
