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
        if (!$this->hasModifier($property)) {
            throw new \InvalidArgumentException("Modifier '{$property}' not found");
        }
        
        return $this->modifiers[$property];
    }
    
    /**
     * @return array
     */
    public function getModifiers()
    {
        return $this->modifiers;
    }
    
    /**
     * Return the name of the modifier
     *
     * @return string
     */
    abstract public function getName();
    
    /**
     * @param $property
     *
     * @return bool
     */
    public function hasModifier($property)
    {
        return isset($this->modifiers[$property]);
    }
    
    /**
     * @param $property
     *
     * @return $this
     */
    public function removeModifier($property)
    {
        if ($this->hasModifier($property)) {
            unset($this->modifiers[$property]);
        }
        
        return $this;
    }
    
    /**
     * @param ServerRequestInterface $request
     *
     * @return $this
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
        if (isset($modifiers['property'])) {
            $modifiers = [$modifiers];
        }
        
        $this->modifiers = $modifiers;
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
     * Has the modifier all required data to be applied?
     *
     * @param array $modifier
     *
     * @return bool
     */
    abstract protected function hasAllRequiredData(array $modifier);
}
