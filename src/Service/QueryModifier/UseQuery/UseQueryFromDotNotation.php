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
     * @var string
     */
    protected $dotProperty;
    /**
     * @var bool
     */
    protected $inUse = false;
    /**
     * @var array
     */
    protected $map = [];
    /**
     * @var string
     */
    protected $property;
    /**
     * @var ModelCriteria
     */
    protected $query;

    public function __construct(ModelCriteria $query, string $dotProperty)
    {
        $cleanDotProperty = trim($dotProperty);
        # Remove first dot, in this case this is NO relation, only property
        $cleanDotProperty = ltrim($cleanDotProperty, ".");

        $this->map = explode(self::RELATION_SEP, $cleanDotProperty);

        $this->query       = $query;
        $this->dotProperty = $cleanDotProperty;
        $this->property    = array_pop($this->map);
        $this->depth       = count($this->map);

        if (empty($this->property)) {
            throw new \InvalidArgumentException("Property is empty in \"$dotProperty\"");
        }
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
     * @return string ucfirst string
     */
    public function getProperty(): string
    {
        return ucfirst($this->property);
    }

    /**
     * @return bool
     */
    public function isInUse(): bool
    {
        return $this->inUse;
    }

    /**
     * @return ModelCriteria
     * @throws UseQueryFromDotNotationException
     */
    public function useQuery(): ModelCriteria
    {
        if ($this->inUse) {
            throw new UseQueryFromDotNotationException("A query is already in use and have not be terminated with UseQueryFromDotNotation::endUse()");
        }

        if ($this->depth) {
            foreach ($this->map as $relation) {
                $method = sprintf('use%sQuery', ucfirst($relation));
                if (!method_exists($this->query, $method)) {
                    throw new RelationNotFoundException("Relation \"$relation\" Not Found in \"$this->dotProperty\"");
                }
                $this->query = call_user_func([$this->query, $method]);
            }
        }
        $this->inUse = true;

        return $this->query;
    }
}
