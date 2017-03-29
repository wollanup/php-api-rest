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
     * @param ModelCriteria $query
     *
     * @return ModelCriteria
     */
    public function apply(ModelCriteria $query);
}
