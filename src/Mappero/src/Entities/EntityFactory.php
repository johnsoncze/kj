<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Entities;

use Nette\Utils\DateTime;
use Ricaefeliz\Mappero\Annotations\Annotation;
use Ricaefeliz\Mappero\Annotations\AnnotationManager;
use Nette\Database\Table\IRow;
use App\NObject;
use Ricaefeliz\Mappero\Annotations\Relation;
use Ricaefeliz\Mappero\Annotations\Table;
use Ricaefeliz\Mappero\Helpers\Entities;
use Ricaefeliz\Mappero\Mapping\Translation\ITranslationMapping;
use Ricaefeliz\Mappero\Mapping\Translation\TranslationMapping;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @todo TranslationMapping předávám skrze DI jako ITranslationMapping
 * @todo tuto třídu napsat do objektů
 */
class EntityFactory extends NObject
{


    /** @var AnnotationManager */
    protected $annotationManager;

    /** @var ITranslationMapping */
    protected $translationMapping;



    public function __construct(AnnotationManager $annotationManager)
    {
        $this->annotationManager = $annotationManager;
        $this->translationMapping = new TranslationMapping();
    }



    /**
     * @param $annotation Annotation
     * @param IRow $row
     * @return IEntity
     */
    public function create(Annotation $annotation, IRow $row)
    {
        $entityName = $annotation->getEntityName();
        $entityObject = new $entityName();

        return $this->setDataToEntity($entityObject, $annotation, $row);
    }



    /**
     * @param $annotation Annotation
     * @param $rows IRow[]
     * @return IEntity[]
     */
    public function createMore(Annotation $annotation, $rows)
    {
        $entities = [];
        foreach ($rows as $row) {
            $entityObject = $this->create($annotation, $row);
            if ($annotation->getTable()->getType() == Table::VIEW_TYPE) {
                $entities[] = $entityObject;
                continue;
            }
            $entities[$entityObject->getId()] = $entityObject;
        }
        return $entities;
    }



    /**
     * @param IEntity $entity
     * @param Annotation $annotation
     * @param IRow $row
     * @return IEntity
     */
    protected function setDataToEntity(IEntity $entity, Annotation $annotation, IRow $row)
    {
        foreach ($annotation->getProperties() as $property) {
            $dataForSet = NULL;
            if ($relation = $property->getRelation()) {

                //Get annotation of relation entity
                $relationEntityAnnotation = $this->annotationManager->getAnnotation($relation->getRelationEntity());

                //One to one
                if ($relation->getRelation() == Relation::ONE_TO_ONE) {
                    $relationTable = $relationEntityAnnotation->getTable()->getName();
                    if ($relation->getReferencedProperty()) {
                        $relatedRow = $row->related($relationTable, $relation->getReferencedProperty()->getColumn()->getName());
                        $relatedRow = end($relatedRow);
                    } else {
                        $relatedRow = $row->ref($relationTable);
                    }

                    if ($relatedRow) {
                        $relatedEntityName = $relation->getRelationEntity();
                        $relatedEntity = new $relatedEntityName();
                        $dataForSet = $this->setDataToEntity($relatedEntity, $relationEntityAnnotation, $relatedRow);
                    }
                } //One to many
                elseif ($relation->getRelation() == Relation::ONE_TO_MANY) {
                    $relationEntities = [];
                    if ($relatedRows = $row->related($relationEntityAnnotation->getTable()->getName())) {
                        foreach ($relatedRows as $relatedRow) {
                            $relatedEntityName = $relation->getRelationEntity();
                            $relatedEntity = new $relatedEntityName();
                            $this->setDataToEntity($relatedEntity, $relationEntityAnnotation, $relatedRow);
                            if ($property->getTranslation()) {
                                $this->translationMapping->map($entity, $relatedEntity);
                            }
                            $relationEntities[$relatedEntity->getId()] = $relatedEntity;
                        }
                        $dataForSet = $relationEntities;
                    }
                }
            } else {
                $dataForSet = $row[$property->getColumn()->getName()];
            }
            if ($dataForSet !== NULL) {
                $dataForSet = $dataForSet instanceof DateTime ? (string)$dataForSet : $dataForSet;
            }
            Entities::setProperty($entity, $property->getName(), $dataForSet);
        }
        return $entity;
    }


}