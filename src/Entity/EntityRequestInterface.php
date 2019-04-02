<?php

namespace Eukles\Entity;

use Eukles\Action\ActionInterface;
use Eukles\Container\ContainerInterface;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Map\Exception\RelationNotFoundException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Psr\Http\Message\RequestInterface;

/**
 * Interface ActiveRecordRequestInterface
 *
 * @package Core\Model
 */
interface EntityRequestInterface
{

    /**
     * ActiveRecordRequestTrait constructor.
     *
     * @param RequestInterface $request
     */
    public function __construct(RequestInterface $request);

    /**
     * @return RequestInterface
     */
    public function getRequest(): RequestInterface;

    /**
     * @param RequestInterface $request
     * @return EntityRequestAbstract
     */
    public function setRequest(RequestInterface $request): EntityRequestAbstract;

    /**
     * @param \Psr\Container\ContainerInterface|ContainerInterface $c
     *
     * @return mixed
     */
    public function setContainer(\Psr\Container\ContainerInterface $c);

    /**
     * Set state of the object after request data hydration
     *
     * @param ActiveRecordInterface $obj
     * @param RequestInterface $request
     */
    public function afterCreate(ActiveRecordInterface $obj, RequestInterface $request);

    /**
     * Set state of the object after request data hydration
     *
     * @param ActiveRecordInterface $obj
     * @param RequestInterface $request
     */
    public function afterFetch(ActiveRecordInterface $obj, RequestInterface $request);

    /**
     * Set state of the object before request data hydration
     *
     * @param ActiveRecordInterface $obj
     * @param RequestInterface $request
     */
    public function beforeCreate(ActiveRecordInterface $obj, RequestInterface $request);

    /**
     * Set state of the object before request data hydration
     *
     * @param ModelCriteria $query
     *
     * @param RequestInterface $request
     * @return ModelCriteria
     */
    public function beforeFetch(ModelCriteria $query, RequestInterface $request);

    /**
     *
     * @return string|ActionInterface
     */
    public function getActionClassName();

    /**
     * List data usable from request, may vary according to HTTP verb
     *
     * @param array $requestParams
     * @param string $httpMethod
     *
     * @return array
     */
    public function getAllowedDataFromRequest(array $requestParams, $httpMethod);

    /**
     * @return ContainerInterface
     */
    public function getContainer();

    /**
     * None, all or partial list of properties
     *
     * @return array List of exposed properties
     */
    public function getExposedProperties();

    /**
     * None, all or partial list of relations
     *
     * @return array List of exposed relations
     */
    public function getExposedRelations();

    /**
     * None, all or partial list of properties
     *
     * @return array List of modifiable properties
     */
    public function getModifiableProperties();

    /**
     * @return mixed
     */
    public function getPrimaryKey();

    /**
     * Gets a RelationMap of the table by relation name
     * This method will build the relations if they are not built yet
     *
     * @param string $relation The relation name
     *
     * @return RelationMap                         The relation object
     * @throws RelationNotFoundException When called on an inexistent relation
     */
    public function getRelation($relation);

    /**
     * Gets the type of the relation (on to one, one to many ...)
     *
     * @param string $relation The relation name
     *
     * @return string Type of the relation (on to one, one to many ...)
     *
     */
    public function getRelationType($relation);

    /**
     * @return RelationMap[] array
     *
     */
    public function getRelations();

    /**
     * Gets names of the relations in CAMELNAME format (e.g. "myRelation")
     *
     * @return array
     */
    public function getRelationsNames();

    /**
     * None, all or partial list of properties
     *
     * @return array List of writable properties
     */
    public function getRequiredWritableProperties();

    /**
     * @return TableMap
     */
    public function getTableMap();

    /**
     * None, all or partial list of properties
     *
     * @return array List of writable properties
     */
    public function getWritableProperties();

    /**
     * @return bool
     */
    public function hasRelations();

    /**
     * @return ActiveRecordInterface
     */
    public function instantiateActiveRecord();

    /**
     * Does this relation is plural ?
     *
     * @param string $relation Name of the relation in CAMELNAME format (e.g. "myRelation")
     *
     * @return bool
     * @throws RelationNotFoundException When called on an inexistent relation
     */
    public function isPluralRelation($relation);

    /**
     * Does this property is a relation ?
     *
     * @param string $relation Name of the relation in CAMELNAME format (e.g. "myRelation")
     *
     * @return bool
     */
    public function isRelation($relation);

    /**
     * @param $name
     *
     * @return bool
     */
    public function isRelationManyToOne($name);

    /**
     * @param $name
     *
     * @return bool
     */
    public function isRelationOneToMany($name);

    /**
     * @param $name
     *
     * @return bool
     */
    public function isRelationOneToOne($name);

    /**
     * @param array|int $pk
     *
     * @return EntityRequestInterface
     */
    public function setPrimaryKey($pk);

    /**
     * @return ActiveRecordInterface|string
     */
    public function getActiveRecordClassName();

    /**
     *
     * @param bool $plural
     *
     * @return string
     */
    public static function getNameOfParameterToAdd($plural = false);
}
