<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 05/01/18
 * Time: 12:47
 */

namespace Eukles\Service\QueryModifier\Util;

use Propel\Runtime\ActiveQuery\ModelCriteria;

abstract class EasyUtil
{

    const RELATION_SEP = '.';
    /**
     * @var bool
     */
    protected $autoUseRelationQuery = false;
    /**
     * @var string
     */
    protected $property;
    /**
     * @var ModelCriteria
     */
    protected $query;
    /**
     * @var array
     */
    protected $relations = [];

    /**
     * EasyFilter constructor.
     *
     * @param ModelCriteria $query
     */
    public function __construct(ModelCriteria $query)
    {
        $this->query = $query;
    }

    abstract protected function filter();


    /**
     * @param ModelCriteria $query
     *
     * @return EasyUtil
     */
    public static function create(ModelCriteria $query): EasyUtil
    {
        return new static($query);
    }

    /**
     * @return ModelCriteria
     */
    public function getQuery(): ModelCriteria
    {
        return $this->query;
    }

    /**
     * @return bool
     */
    public function isAutoUseRelationQuery(): bool
    {
        return $this->autoUseRelationQuery;
    }

    /**
     * @param bool $autoUseRelationQuery
     *
     * @return static
     */
    public function setAutoUseRelationQuery(bool $autoUseRelationQuery): EasyUtil
    {
        $this->autoUseRelationQuery = $autoUseRelationQuery;

        return $this;
    }

    /**
     * @return bool
     */
    protected function useRelationQuery()
    {
        $map            = explode(self::RELATION_SEP, $this->property);
        $this->property = array_pop($map);

        if (count($map) > 0) {
            $this->relations = $map;
            foreach ($this->relations as $relation) {
                $method = sprintf('use%sQuery', ucfirst($relation));
                if (!method_exists($this->query, $method)) {
                    return false;
                }
                $this->query = call_user_func([$this->query, $method]);
            }

            $result = $this->filter();

            foreach ($this->relations as $null) {
                $this->query = $this->query->endUse();
            }
        } else {
            return $this->filter();
        }

        return $result;
    }
}
