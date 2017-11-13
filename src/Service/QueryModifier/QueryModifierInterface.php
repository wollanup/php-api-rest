<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 28/03/17
 * Time: 15:10
 */

namespace Eukles\Service\QueryModifier;

use Propel\Runtime\ActiveQuery\ModelCriteria;

interface QueryModifierInterface
{

    /**
     * Applies modifiers and merge query if one has been set
     *
     * @param ModelCriteria $query
     *
     * @return ModelCriteria
     */
    public function apply(ModelCriteria $query);

    /**
     * This ModelCriteria will be merged with another one when apply is called
     *
     * @param ModelCriteria $query
     *
     * @return QueryModifierInterface
     */
    public function setQuery(ModelCriteria $query): QueryModifierInterface;
}
