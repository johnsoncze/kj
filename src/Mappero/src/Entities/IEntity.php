<?php

namespace Ricaefeliz\Mappero\Entities;

use Ricaefeliz\Mappero\Annotations\Annotation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface IEntity
{


    /**
     * @param $id int
     * @return self
     */
    public function setId($id);



    /**
     * @return int|null
     */
    public function getId();



    /**
     * @return array
     */
    public function toArray();



    /**
     * @return string name with namespace
     */
    public function getClassName();



    /**
     * @return Annotation
     */
    public static function getAnnotation();
}