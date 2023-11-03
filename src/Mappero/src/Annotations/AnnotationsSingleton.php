<?php

namespace Ricaefeliz\Mappero\Annotations;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class AnnotationsSingleton extends NObject
{


    /** @var array */
    protected static $annotations = [];



    /**
     * @param $name string
     * @return Annotation|null
     */
    public static function getAnnotation($name)
    {
        if (!isset(self::$annotations[$name])) {
            $am = new AnnotationManager();
            return $am->getAnnotation($name);
        }
        return self::$annotations[$name];
    }



    /**
     * @param Annotation $annotation
     * @return Annotation
     */
    public static function saveAnnotation(Annotation $annotation)
    {
        self::$annotations[$annotation->getEntityName()] = $annotation;
        return $annotation;
    }
}