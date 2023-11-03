<?php

declare(strict_types = 1);

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;
use Ricaefeliz\Mappero\Exceptions\AnnotationException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @method getTable()
 * @method getPrimaryProperty()
 * @method getEntityName()
 */
class Annotation extends NObject
{


    /** @var string */
    protected $entityName;

    /** @var Table|null */
    protected $table;

    /** @var Property */
    protected $primaryProperty;

    /** @var array|Property[] */
    protected $properties = [];

    /** @var array|Property[] */
    protected $propertiesSortByPropertyName = [];



    public function __construct(string $entityName)
    {
        $this->entityName = $entityName;
    }



    /**
     * @param $table Table
     * @return $this
     */
    public function setTable(Table $table)
    {
        $this->table = $table;
        return $this;
    }



    /**
     * @param Property $primaryProperty
     * @return $this
     */
    public function setPrimaryProperty(Property $primaryProperty)
    {
        $this->primaryProperty = $primaryProperty;
        return $this;
    }



    /**
     * @param Property $property
     * @return $this
     */
    public function addProperty(Property $property)
    {
        $this->properties[] = $property;
        $this->propertiesSortByPropertyName[$property->getName()] = $property;
        return $this;
    }



    /**
     * @return array|Property[]
     */
    public function getProperties()
    {
        return $this->properties;
    }



    /**
     * @param $name
     * @return Property|null
     */
    public function getPropertyByName($name)
    {
        if(isset($this->propertiesSortByPropertyName[$name])){
            return $this->propertiesSortByPropertyName[$name];
        }
        return NULL;
    }



    /**
     * @param array|NULL $properties
     * @return array|null
     * @throws AnnotationException
     */
    public function getColumnsToArray(array $properties = NULL)
    {

        if ($this->properties) {
            $columns = [];
            if ($properties) {
                foreach ($properties as $property) {
                    if (!isset($this->propertiesSortByPropertyName[$property])) {
                        throw new AnnotationException("Unknown property '$property'.");
                    } elseif (!$this->propertiesSortByPropertyName[$property]->getColumn()) {
                        throw new AnnotationException("Property '$property' does not have column.");
                    }
                    $columns[] = $this->propertiesSortByPropertyName[$property]->getColumn();
                }
            } else {
                foreach ($this->properties as $property) {
                    if ($column = $property->getColumn()) {
                        $columns[] = $property->getColumn();
                    }
                }
            }
            return $columns;
        }
        return NULL;
    }
}