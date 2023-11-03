<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Helpers;

use Nette\Database\Table\IRow;
use App\NObject;
use Ricaefeliz\Mappero\Annotations\Column;
use Ricaefeliz\Mappero\Annotations\PropertyException;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Exceptions\HelperException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Entities extends NObject
{


    /**
     * @param $entity IEntity
     * @param $property string
     * @return bool
     * @throws PropertyException
     */
    public static function hasProperty(IEntity $entity, string $property) : bool
    {
        if (!property_exists($entity, $property)) {
            throw new PropertyException(sprintf('Class \'%s\' not has property \'%s\'.', get_class($entity), $property));
        }
        return TRUE;
    }



    /**
     * @param IEntity $entity
     * @param $row IRow
     * @return IEntity
     */
    public static function setId(IEntity $entity, IRow $row) : IEntity
    {
        $annotation = $entity::getAnnotation();
        $primaryProperty = $annotation->getPrimaryProperty();
        $entity->setId($row[$primaryProperty->getColumn()->getName()]);
        return $entity;
    }



    /**
     * @param array $entities
     * @return array
     * @throws HelperException
     */
    public static function getId(array $entities) : array
    {
        $id = [];
        foreach ($entities as $entity) {
            if (!$entity->getId()) {
                throw new HelperException("Missing id of entity '{$entity->getClassName()}'.");
            }
            $id[] = $entity->getId();
        }
        return $id;
    }



    /**
     * @param $entities IEntity[]
     * @param $row IRow
     * @return IEntity[]
     */
    public static function setAscendId(array $entities, IRow $row) : array
    {
        $annotation = $entities[0]::getAnnotation();
        $primaryProperty = $annotation->getPrimaryProperty();
        $start = $row[$primaryProperty->getColumn()->getName()];
        foreach ($entities as $entity) {
            $entity->setId($start);
            $start++;
        }
        return $entities;
    }



    /**
     * @param $entity IEntity
     * @param $property string
     * @param $value string|null
     * @return IEntity
     */
    public static function setProperty(IEntity $entity, $property, $value = NULL)
    {
        if ($value !== NULL) {
            if ($value instanceof \DateInterval) {
                $value = $value->format("%H:%I:%S");
            } elseif (is_string($value)) {
                $value = html_entity_decode($value, ENT_QUOTES);
            }
            $entity->{"set" . ucfirst($property)}($value);
        }
        return $entity;
    }



    /**
     * @param IEntity $entity
     * @return array
     * @throws HelperException
     */
    public static function getArrayForSave(IEntity $entity)
    {
        $annotation = $entity::getAnnotation();
        if (!$properties = $annotation->getProperties()) {
            throw new HelperException("Missing properties.");
        }
        $data = [];
        foreach ($properties as $property) {
            $column = $property->getColumn();
            if ($column && $column->getType() !== Column::TYPE_TIMESTAMP) {
                $data[$column->getName()] = $entity->{"get" . ucfirst($property->getName())}();
            }
        }
        return $data;
    }
}