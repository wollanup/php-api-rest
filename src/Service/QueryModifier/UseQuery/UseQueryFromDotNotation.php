<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/01/18
 * Time: 12:31
 */

namespace Eukles\Service\QueryModifier\UseQuery;

use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Map\Exception\RelationNotFoundException;

class UseQueryFromDotNotation
{

    const RELATION_SEP = '.';
    /**
     * @var int
     */
    protected $depth = 0;
    /**
     * @var bool
     */
    protected $inUse = false;
    /**
     * @var array
     */
    protected $map = [];
    /**
     * @var ModelCriteria
     */
    protected $query;

    private static $counter = 0;

    /**
     * UseQueryFromDotNotation constructor.
     * @param ModelCriteria $query
     */
    public function __construct(ModelCriteria $query)
    {
        $this->query = $query;
    }

    /**
     * @param array $relations
     * @return UseQueryFromDotNotation
     */
    public function fromArray(array $relations): self
    {
        if (empty($relations)) {
            return $this;
        }

        $this->map = $relations;
        $this->depth = count($this->map);

        return $this;
    }

    /**
     * @param string $relations
     * @return UseQueryFromDotNotation
     */
    public function fromString(string $relations): self
    {
        $relations = trim($relations);
        # Remove first dot, in this case this is NO relation, only property
        $relations = ltrim($relations, ".");

        if (empty($relations)) {
            return $this;
        }

        $this->map = explode(self::RELATION_SEP, $relations);
        $this->depth = count($this->map);

        return $this;
    }

    /**
     * @return ModelCriteria
     * @throws UseQueryFromDotNotationException
     */
    public function endUse(): ModelCriteria
    {
        if (!$this->inUse) {
            throw new UseQueryFromDotNotationException("No query used from UseQueryFromDotNotation::useQuery()");
        }

        if ($this->depth) {
            for ($i = 0; $i < $this->depth; $i++) {
                $this->query = $this->query->endUse();
            }
        }
        $this->inUse = false;

        return $this->query;
    }

    /**
     * @return int
     */
    public function getDepth(): int
    {
        return $this->depth;
    }

    /**
     * @return bool
     */
    public function isInUse(): bool
    {
        return $this->inUse;
    }

    /**
     * @param null $alias
     * @param string $joinType
     * @return ModelCriteria
     * @throws UseQueryFromDotNotationException
     */
    public function useQuery($alias = null, $joinType = null): ModelCriteria
    {
        if ($this->inUse) {
            throw new UseQueryFromDotNotationException("A query is already in use and have not be terminated with UseQueryFromDotNotation::endUse()");
        }

        if ($this->depth) {
            foreach ($this->map as $relation) {
                $method = sprintf('use%sQuery', ucfirst($relation));
                if (!method_exists($this->query, $method)) {
                    $path = implode(self::RELATION_SEP, $this->map);
                    throw new RelationNotFoundException("Relation \"$relation\" Not Found in \"$path\"");
                }
                $alias = 'alias_' . self::$counter++;
                $this->query = call_user_func([$this->query, $method], "`" . $alias . "_" . $relation . "`", $joinType);
            }
        }
        $this->inUse = true;

        return $this->query;
    }
}
