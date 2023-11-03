<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use Ricaefeliz\Mappero\Exceptions\RelationException;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @method getEntity()
 * @method getRelation()
 * @method getReferencedProperty()
 */
class Relation extends NObject
{


    /** @var string */
    const ONE_TO_ONE = "OneToOne";

    /** @var string */
    const ONE_TO_MANY = "OneToMany";

    /** @var string */
    protected $entity;

    /** @var string */
    protected $relationEntity;

    /** @var string */
    protected $relation;

    /** @var Property|null */
    protected $referencedProperty;

    /** @var array */
    protected static $relations = [
        self::ONE_TO_MANY,
        self::ONE_TO_ONE
    ];



    public function __construct(string $entity, string $relationEntity, string $relation)
    {
        if (!in_array($relation, self::$relations)) {
            throw new RelationException("Unknown type '$relation' of relation. You can use '" . implode(",", self::$relations) . "'.");
        }
        $this->entity = $entity;
        $this->relationEntity = $relationEntity;
        $this->relation = $relation;
    }



    /**
     * @param $property Property
     * @return self
     */
    public function setReferencedProperty(Property $property) : self
    {
        $this->referencedProperty = $property;
        return $this;
    }



    /**
     * @return string
     */
    public function getRelationEntity() : string
    {
        return $this->relationEntity;
    }
}