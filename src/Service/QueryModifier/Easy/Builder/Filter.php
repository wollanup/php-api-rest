<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 12:47
 */

namespace Eukles\Service\QueryModifier\Easy\Builder;

use Propel\Runtime\ActiveQuery\Criteria;

class Filter
{

    /**
     * Used because there is no SQL operator to do a "between" condition
     */
    const BETWEEN = "BETWEEN";
    /**
     * @var mixed
     */
    protected $value;
    /**
     * @var string
     */
    protected $operator;

    /**
     * @param $value
     *
     */
    public function __construct(string $value)
    {
        # Use default operator
        $operator = Criteria::EQUAL;

        # Handle negate operator
        $firstChar = mb_substr($value, 0, 1);
        $negate    = $firstChar === '!';
        if ($negate) {
            $value     = substr($value, 1);
            $operator  = Criteria::NOT_EQUAL;
            $firstChar = mb_substr($value, 0, 1);
        }

        # handle special null value
        if (strtolower($value === "null")) {
            $this->value    = null;
            $this->operator = $negate ? Criteria::ISNOTNULL : Criteria::ISNULL;

            return;
        }

        # Handle LIKE operator when % is present in value
        if ($firstChar === '%' || strpos($value, '%') === strlen($value) - 1) {
            $operator = $negate ? Criteria::NOT_LIKE : Criteria::LIKE;
        }# Handle min/max operator when [ is present
        elseif ($firstChar === '[') {
            $operator = 'BETWEEN';
            $value    = substr($value, 1);
            $lastChar = substr($value, -1);
            if ($lastChar === ']') {
                $value = substr($value, 0, -1);
            }
            $value = explode(',', $value);
            if (empty($value[0])) {
                $operator = $negate ? Criteria::ISNOTNULL : Criteria::ISNULL;
                $value    = null;
            } else {
                $valueTmp = ['min' => $value[0]];
                if (!empty($value[1])) {
                    $valueTmp['max'] = $value[1];
                } else {
                    $valueTmp = $value[0];
                    $operator = Criteria::GREATER_EQUAL;
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
            $operator = $negate ? Criteria::NOT_IN : Criteria::IN;
            $value    = explode(',', $value);
        }

        $this->value    = $value;
        $this->operator = $operator;
    }

    /**
     * @return string
     */
    public function getOperator(): string
    {
        return $this->operator;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }
}
