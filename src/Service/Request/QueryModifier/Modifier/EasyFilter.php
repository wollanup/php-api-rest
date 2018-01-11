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
     * @throws \Eukles\Service\QueryModifier\UseQuery\UseQueryFromDotNotationException
     */
    public function apply(ModelCriteria $query)
    {
        $modifier = new Modifier($query);
        foreach ($this->request->getQueryParams() as $column => $value) {
            # Ignored params
            if (in_array($column, $this->ignoredParams)) {
                continue;
            }
            $filter = new Filter($value);
            $modifier->filterBy($column, $filter->getValue(), $filter->getOperator());
        }
    }
}
