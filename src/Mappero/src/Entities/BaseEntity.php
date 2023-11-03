<?php

namespace App;

use App\Helpers\Entities;
use Ricaefeliz\Mappero\Entities\IEntity;
use Ricaefeliz\Mappero\Annotations\AnnotationManager;
use App\NObject;
use Ricaefeliz\Mappero\Annotations\Annotation;
use Ricaefeliz\Mappero\Annotations\AnnotationsSingleton;
use Ricaefeliz\Mappero\Exceptions\EntityException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
abstract class BaseEntity extends NObject implements \ArrayAccess
{


    protected $id;



    /**
     * @param $id int
     * @return void
     */
    public function setId($id)
    {
        $this->id = $id;
    }



    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id ? (int)$this->id : $this->id;
    }



    /**
     * Mapping entity to array
     * @return array
     */
    public function toArray()
    {
        $calledClass = $this->getClassName();
        $annotationManager = new AnnotationManager();
        $annotation = $annotationManager->getAnnotation($calledClass);

        $data = [];
        foreach ($annotation->getProperties() as $property) {
            $value = $this->getValueOfProperty($property->getName());
            if (is_array($value)) {
                foreach ($value as $v) {
                    if ($v instanceof IEntity) {
                        $data[$property->getName()][$v->getId()] = $v->toArray();
                    }
                }
            } elseif ($value instanceof IEntity) {
                $data[$property->getName()] = $value->toArray();
            } else {
                $data[$property->getName()] = $value;
            }
        }
        return $data;
    }



    /**
     * @param $property string
     * @return mixed
     */
    protected function getValueOfProperty(string $property)
    {
        return $this->{Entities::getGetterMethodName($property)}();
    }



    /**
     * @return string
     */
    public function getClassName()
    {
        return get_called_class();
    }



    /**
     * @return Annotation
     */
    public static function getAnnotation()
    {
        return AnnotationsSingleton::getAnnotation(get_called_class());
    }


    /*************** ArrayAccess interface ***************/

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return property_exists($this->getClassName(), $offset);
    }



    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->getValueOfProperty($offset);
    }



    /**
     * @inheritdoc
     * @throws EntityException
     */
    public function offsetSet($offset, $value)
    {
        throw new EntityException('You can not set value through array. Use "set" method.');
    }



    /**
     * @inheritdoc
     * @throws EntityException
     */
    public function offsetUnset($offset)
    {
        throw new EntityException('You can not do unset action.');
    }
}