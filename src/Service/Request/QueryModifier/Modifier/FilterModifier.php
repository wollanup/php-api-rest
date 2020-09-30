<?php
/**
 * File description
 *
 * @package
 * @version      $LastChangedRevision:$
 *               $LastChangedDate:$
 * @link         $HeadURL:$
 * @author       $LastChangedBy:$
 */

namespace Eukles\Service\Request\QueryModifier\Modifier;

use Eukles\Service\QueryModifier\Easy\Builder\Filter;
use Eukles\Service\QueryModifier\Easy\Modifier;
use Eukles\Service\QueryModifier\Modifier\Exception\ModifierException;
use Eukles\Service\Request\QueryModifier\Modifier\Base\ModifierBase;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class FilterModifier
 *
 * @package Ged\Service\RequestQueryModifier
 */
class FilterModifier extends ModifierBase
{

    const NAME = 'filter';
    /**
     * Allowed filter operators that can be use by a client
     *
     * @var array
     */
    protected static $allowedFilterOperators
        = [
            Criteria::EQUAL,
            Criteria::NOT_EQUAL,
            Criteria::ALT_NOT_EQUAL,
            Criteria::GREATER_EQUAL,
            Criteria::GREATER_THAN,
            Criteria::LESS_EQUAL,
            Criteria::LESS_THAN,
            Criteria::IN,
            Criteria::NOT_IN,
            Criteria::LIKE,
            Criteria::NOT_LIKE,
            Criteria::ILIKE,
            Criteria::NOT_ILIKE,
        ];
    /**
     * @var array
     */
    private $allowedOperators;

    /**
     * FilterModifier constructor.
     *
     * Note : We need to trim Propel criterion constants values because values of modifiers in request are trimmed as well
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        foreach (self::$allowedFilterOperators as $operator){
            $this->allowedOperators[trim($operator)] = $operator;
        }
        parent::__construct($request);
    }


    /**
     * Return the name of the modifier
     *
     * @return string
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * Apply the filter to the ModelQuery
     *
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     *
     * @return Modifier
     */
    public function apply(ModelCriteria $query)
    {
        $modifierClass = new Modifier($query);
        if (!empty($this->modifiers)) {
            foreach ($this->modifiers as $modifier) {
                if ($this->hasAllRequiredData($modifier)) {
                    $operator = array_key_exists('operator', $modifier) ? $this->allowedOperators[trim($modifier['operator'])] : null;
                    if ($modifier['value'] === null) {
                        if (!array_key_exists('operator', $modifier) || $modifier['operator'] === Criteria::EQUAL) {
                            $operator = Criteria::ISNULL;
                        } else {
                            $operator = Criteria::ISNOTNULL;
                        }
                    }
                    $modifierClass->filterBy($modifier['property'], $modifier['value'], $operator);

                }
            }
        }
        return $modifierClass;
    }

    /**
     * Has the modifier all required data to be applied?
     *
     * @param array $modifier
     *
     * @return bool
     * @throws ModifierException
     */
    protected function hasAllRequiredData(array $modifier)
    {
        if (array_key_exists('operator', $modifier)
            && !array_key_exists($modifier['operator'],
                                 $this->allowedOperators)
        ) {
            throw new ModifierException('The filter operator "' . $modifier['operator'] . '" is not allowed. You can only use one of the following:
                        ' . implode(', ', self::$allowedFilterOperators));
        }
        return array_key_exists('property', $modifier) && array_key_exists('value', $modifier);
    }
}
