<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 04/04/17
 * Time: 10:52
 */

namespace Eukles\Service\QueryModifier;

use Propel\Runtime\ActiveQuery\ModelCriteria;

class QueryModifier implements QueryModifierInterface
{

    /**
     * @var ModelCriteria
     */
    protected $query;
    
    /**
     * Applies modifiers and merge query if one has been set
     *
     * @param ModelCriteria $query
     *
     * @return ModelCriteria
     */
    public function apply(ModelCriteria $query)
    {
        if ($this->query) {
            $query->mergeWith($this->query);
        }
    
        return $query;
    }
    
    /**
     * This ModelCriteria will be merged with another one when apply is called
     *
     * @param ModelCriteria $query
     *
     * @return QueryModifierInterface
     */
    public function setQuery(ModelCriteria $query)
    {
        $this->query = $query;
    
        return $this;
    }
}
