<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 12:47
 */

namespace Eukles\Service\QueryModifier\Util;

use Propel\Runtime\ActiveQuery\Criteria;

class EasyFilter extends EasyUtil
{

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param $value
     *
     * @return array
     */
    public static function build($value)
    {
        # Use default operator
        $operator = null;

        # Handle negate operator
        $firstChar = mb_substr($value, 0, 1);
        $negate    = $firstChar === '!';
        if ($negate) {
            $value     = substr($value, 1);
            $operator  = Criteria::NOT_EQUAL;
            $firstChar = mb_substr($value, 0, 1);
        }

        # Handle LIKE operator when % is present in value
        if ($firstChar === '%' || strpos($value, '%') === strlen($value) - 1) {
            $operator = $negate ? Criteria::NOT_LIKE : Criteria::LIKE;
        }# Handle min/max operator when [ is present
        elseif ($firstChar === '[') {
            $operator = null;
            $value    = substr($value, 1);
            $lastChar = substr($value, -1);
            if ($lastChar === ']') {
                $value = substr($value, 0, -1);
            }
            $value = explode(',', $value);
            if (empty($value[0])) {
                $value = null;
            } else {
                $valueTmp = ['min' => $value[0]];
                if (!empty($value[1])) {
                    $valueTmp['max'] = $value[1];
                }
                $value = $valueTmp;
            }
        } # Handle > operators
        elseif ($firstChar === '>') {
            $value    = substr($value, 1);
            $operator = Criteria::GREATER_THAN;
            if (mb_substr($value, 0, 1) === '=') {
                $value    = substr($value, 1);
                $operator = Criteria::GREATER_EQUAL;
            }
        } # Handle < operators
        elseif ($firstChar === '<') {
            $value    = substr($value, 1);
            $operator = Criteria::LESS_THAN;
            if (strpos($value, '=') === 0) {
                $value    = substr($value, 1);
                $operator = Criteria::LESS_EQUAL;
            }
        } elseif ($firstChar === '"' || $firstChar === "'") {
            $value = trim($value, "\"'");
        } # Handle IN operator when comma is present
        elseif (strpos($value, ',') !== false) {
            # IN operator is handled by propel
            $operator = $negate ? Criteria::NOT_IN : null;
            $value    = explode(',', $value);
        }

        return [$operator, $value];
    }

    /**
     * @param $property
     * @param $value
     *
     * @return bool
     */
    public function apply($property, $value): bool
    {
        $this->value    = $value;
        $this->property = $property;
        if ($this->isAutoUseRelationQuery()) {
            return $this->useRelationQuery();
        } else {
            return $this->filter();
        }
    }

    /**
     * @return bool
     */
    protected function filter()
    {
        # Determine if method is callable in Query class
        $method = 'filterBy' . ucfirst($this->property);
        if (!method_exists($this->query, $method)) {
            return false;
        }

        list($value, $operator) = $this->build($this->value);

        $this->query = call_user_func([$this->query, $method], $value, $operator);

        return true;
    }
}
