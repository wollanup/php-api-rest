<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 12:47
 */

namespace Eukles\Service\QueryModifier\Util;

use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\Exception\UnknownColumnException;
use Propel\Runtime\ActiveQuery\Exception\UnknownModelException;

class EasySort extends EasyUtil
{

    /**
     * @var string
     */
    protected $direction;

    /**
     * @param $property
     *
     * @return array
     */
    public static function build($property)
    {
        $direction = Criteria::ASC;
        if (strpos($property, '+') === 0) {
            $property = substr($property, 1);
        } elseif (strpos($property, '-') === 0) {
            $property  = substr($property, 1);
            $direction = Criteria::DESC;
        }

        return [$property, $direction];
    }

    public function apply($sort): bool
    {
        list($this->property, $this->direction) = self::build($sort);
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
        # Try to call method in Query class
        try {
            $this->query = $this->query->orderBy(ucfirst($this->property), $this->direction);
        } catch (UnknownColumnException $e) {
            return false;
        } catch (UnknownModelException $e) {
            return false;
        }

        return true;
    }
}
