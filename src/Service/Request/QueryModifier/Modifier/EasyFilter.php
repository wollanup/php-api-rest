<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 01/12/17
 * Time: 14:39
 */

namespace Eukles\Service\Request\QueryModifier\Modifier;

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
     */
    public function apply(ModelCriteria $query)
    {
        $filter = new \Eukles\Service\QueryModifier\Util\EasyFilter($query);
        $filter->setAutoUseRelationQuery(true);

        foreach ($this->request->getQueryParams() as $column => $value) {
            # Ignored params
            if (in_array($column, $this->ignoredParams)) {
                continue;
            }
            $filter->apply($column, $value);
        }
    }
}
