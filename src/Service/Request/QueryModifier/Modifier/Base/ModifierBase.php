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

namespace Eukles\Service\Request\QueryModifier\Modifier\Base;

use Eukles\Service\Request\GetParam;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class ModifierBase
 *
 * @package Ged\Service\RequestQueryModifier
 */
abstract class ModifierBase
{

    use GetParam;
    /**
     * Name of the modifier
     */
    const NAME = '';
    /** @var array */
    protected $modifiers = [];

    /**
     * ModifierBase constructor.
     *
     * @param ServerRequestInterface $request
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->setModifierFromRequest($request);
    }

    /**
     * Apply modifiers
     *
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     * @param string                                    $clause
     * @param array                                     $modifier
     *
     * @return void
     */
    abstract protected function applyModifier(ModelCriteria $query, $clause, array $modifier);

    /**
     * Return the name of the modifier
     *
     * @return string
     */
    abstract public function getName();

    /**
     * Has the modifier all required data to be applied?
     *
     * @param array $modifier
     *
     * @return bool
     */
    abstract protected function hasAllRequiredData(array $modifier);

    /**
     * Apply the filter to the ModelQuery
     *
     * @param \Propel\Runtime\ActiveQuery\ModelCriteria $query
     */
    public function apply(ModelCriteria $query)
    {
        if (!empty($this->modifiers)) {
            foreach ($this->modifiers as $modifier) {
                if ($this->hasAllRequiredData($modifier)) {
                    $modifier['property'] = str_replace('/', '.', $modifier['property']);
                    # Check if the filter is occurring on a related model
                    if (strpos($modifier['property'], '.') !== false) {
                        $propertyParts = explode('.', $modifier['property']);

                        # The last part is the related property we want to filter with, so remove it from the parts and store into a variable
                        $propertyField = array_pop($propertyParts);
                        # The new last part is the relation name
                        $relationName = array_pop($propertyParts);

                        # Apply the modifier
                        $this->applyModifier($query, $this->buildClause($propertyField, $relationName), $modifier);
                    } else {
                        # Apply the modifier
                        $this->applyModifier($query,
                            $this->buildClause($modifier['property'], $query->getModelShortName()),
                            $modifier);
                    }
                }
            }
        }
    }

    /**
     * @param $property
     *
     * @return array
     */
    public function getModifier($property)
    {
        $i = $this->indexOf($property);

        if ($i === -1) {
            throw new \InvalidArgumentException("Modifier '{$property}' not found");
        }

        return $this->modifiers[$i];
    }

    /**
     * @return array
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }

    /**
     * @param $property
     *
     * @return bool
     */
    public function hasModifier($property)
    {
        return $this->indexOf($property) > -1;
    }

    /**
     * @param $property
     *
     * @return $this
     */
    public function removeModifier($property)
    {
        $i = $this->indexOf($property);
        if ($i > -1) {
            unset($this->modifiers[$i]);
        }

        return $this;
    }

    /**
     * @param ServerRequestInterface $request
     *
     */
    public function setModifierFromRequest(ServerRequestInterface $request)
    {
        $this->modifiers = [];

        $modifiers = $this->getParam($request, $this->getName());

        if (empty($modifiers)) {
            return;
        }

        if (is_string($modifiers)) {
            $modifiers = json_decode($modifiers, true);
            if (empty($modifiers)) {
                return;
            }
        } elseif (is_scalar($modifiers)) {
            return;
        }

        # Look if there is a filter over just one field, push it into an array to trick the foreach
        if (!array_key_exists(0, $modifiers)) {
            $modifiers = [$modifiers];
        }

        $this->modifiers = $modifiers;
    }

    /**
     * @param      $property
     * @param null $modelOrRelationName
     *
     * @return string
     */
    protected function buildClause($property, $modelOrRelationName)
    {
        $clause = ucfirst($property);
        if ($modelOrRelationName) {
            $clause = sprintf('%s.%s', ucfirst($modelOrRelationName), $clause);
        }

        return $clause;
    }

    /**
     * @param $property
     *
     * @return int
     */
    private function indexOf($property)
    {
        foreach ($this->modifiers as $i => $modifier) {
            if ($modifier['property'] === $property) {
                return $i;
            }
        }

        return -1;
    }
}
