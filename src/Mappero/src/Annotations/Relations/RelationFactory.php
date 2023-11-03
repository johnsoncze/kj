<?php

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Nette\Utils\ArrayHash;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class RelationFactory extends NObject
{


    /** @var AnnotationFactory */
    protected $annotationFactory;



    public function __construct(AnnotationFactory $annotationFactory)
    {
        $this->annotationFactory = $annotationFactory;
    }



    /**
     * @param ArrayHash $relation
     * @return Relation
     */
    public function createOneToOne(ArrayHash $relation) : Relation
    {
        $_relation = new Relation($relation->relationEntity, $relation->entity, Relation::ONE_TO_ONE);
        $referencedColumn = $relation->referencedColumn ?? NULL;
        if ($referencedColumn) {
            $relationEntityAnnotation = $this->annotationFactory->create($relation->entity);
            $_relation->setReferencedProperty($relationEntityAnnotation->getPropertyByName($referencedColumn));
        }
        return $_relation;
    }



    /**
     * @param ArrayHash $relation
     * @return Relation
     */
    public function createOneToMany(ArrayHash $relation) : Relation
    {
        $relation = new Relation($relation->relationEntity, $relation->entity, Relation::ONE_TO_MANY);
        return $relation;
    }
}