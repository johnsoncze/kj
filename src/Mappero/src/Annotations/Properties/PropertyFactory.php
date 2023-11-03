<?php

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Nette\Reflection\Property AS PropertyReflection;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class PropertyFactory extends NObject
{


    /** @var RelationFactory */
    protected $relationFactory;



    public function __construct(RelationFactory $relationFactory)
    {
        $this->relationFactory = $relationFactory;
    }



    /**
     * @param PropertyReflection $property
     * @param string $entityName
     * @return \Ricaefeliz\Mappero\Annotations\Property|null
     */
    public function create(PropertyReflection $property, string $entityName)
    {
        $columnFactory = new ColumnFactory();
        $relationFactory = $this->relationFactory;
        $propertyObject = new \Ricaefeliz\Mappero\Annotations\Property($property->getName());

        //column
        if ($columnAnnotation = $property->getAnnotation(Column::COLUMN)) {
            $column = $columnFactory->create($columnAnnotation);
            if (isset($columnAnnotation->key) && $columnAnnotation->key == Column::PRIMARY_KEY) {
                $column->setPrimary(TRUE);
            }
            $propertyObject->setColumn($column);
            return $propertyObject;
        } //one to many
        elseif ($relationAnnotation = $property->getAnnotation(Relation::ONE_TO_MANY)) {
            $relationAnnotation->relationEntity = $entityName;
            $relation = $relationFactory->createOneToMany($relationAnnotation);
            $propertyObject->setRelation($relation);

            //Translation
            if ($translation = $property->getAnnotation(Translation::TRANSLATION)) {
                $translationObject = new Translation();
                $propertyObject->setTranslation($translationObject);
            }

            return $propertyObject;
        } //one to one
        elseif ($relationAnnotation = $property->getAnnotation(Relation::ONE_TO_ONE)) {
            $relationAnnotation->relationEntity = $entityName;
            $relation = $relationFactory->createOneToOne($relationAnnotation);
            $propertyObject->setRelation($relation);
            return $propertyObject;
        }
        return NULL;
    }
}