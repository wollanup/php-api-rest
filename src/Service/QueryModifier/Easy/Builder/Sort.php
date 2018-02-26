<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 12:47
 */

namespace Eukles\Service\QueryModifier\Easy\Builder;

use Propel\Runtime\ActiveQuery\Criteria;

class Sort
{

    /**
     * @var string
     */
    protected $direction;
    /**
     * @var string
     */
    protected $property;

    /**
     * @param $property
     */
    public function __construct(string $property)
    {
        $direction = Criteria::ASC;
        if (strpos($property, '+') === 0) {
            $property = substr($property, 1);
        } elseif (strpos($property, '-') === 0) {
            $property  = substr($property, 1);
            $direction = Criteria::DESC;
        }
        if (false === $property || empty($property)) {
            throw new \InvalidArgumentException("Invalid or empty property ");
        }
        $this->direction = $direction;
        $this->property  = $property;
    }

    /**
     * @return string
     */
    public function getDirection(): string
    {
        return $this->direction;
    }

    /**
     * @return string
     */
    public function getProperty(): string
    {
        return $this->property;
    }
}
