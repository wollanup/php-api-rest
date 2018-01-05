<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 01/12/17
 * Time: 14:39
 */

namespace Eukles\Service\Request\QueryModifier\Modifier;

use Propel\Runtime\ActiveQuery\Criteria;
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
        foreach ($this->request->getQueryParams() as $column => $value) {
            # Ignored params
            if (in_array($column, $this->ignoredParams)) {
                continue;
            }

            # Determine if method is callable in Query class
            $method = 'filterBy' . ucfirst($column);
            if (!method_exists($query, $method)) {
                continue;
            }

            # Use default operator
            $operator = Criteria::EQUAL;

            # Handle negate operator
            $negate = strpos($value, '!') === 0;
            if ($negate) {
                $value    = substr($value, 1);
                $operator = Criteria::NOT_EQUAL;
            }

            # Handle LIKE operator when % is present in value
            if (strpos($value, '%') === 0 || strpos($value, '%') === strlen($value) - 1) {
                $operator = $negate ? Criteria::NOT_LIKE : Criteria::LIKE;
            }# Handle IN operator when comma is present
            elseif (strpos($value, ',') !== false) {
                # IN operator is handled by propel
                $operator = null;
                $value    = explode(',', $value);
            } # Handle > operators
            elseif (strpos($value, '>') === 0) {
                $value    = substr($value, 1);
                $operator = Criteria::GREATER_THAN;
                if (strpos($value, '=') === 0) {
                    $value    = substr($value, 1);
                    $operator = Criteria::GREATER_EQUAL;
                }
            } # Handle < operators
            elseif (strpos($value, '<') === 0) {
                $value    = substr($value, 1);
                $operator = Criteria::LESS_THAN;
                if (strpos($value, '=') === 0) {
                    $value    = substr($value, 1);
                    $operator = Criteria::LESS_EQUAL;
                }
            }

            call_user_func([$query, $method], $value, $operator);
        }
    }
}
