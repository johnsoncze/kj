<?php

namespace Ricaefeliz\Mappero\Helpers;

use App\NObject;
use Ricaefeliz\Mappero\Annotations\Annotation;
use Ricaefeliz\Mappero\Annotations\Property;
use Ricaefeliz\Mappero\Exceptions\HelperException;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Columns extends NObject
{


    /**
     * @param Annotation $annotation
     * @param array|NULL $properties
     * @return array
     * @throws HelperException
     */
    public static function getColumns(Annotation $annotation, array $properties = NULL)
    {
        if ($properties) {
            $columns = [];
            foreach ($properties as $property) {

                //For example: COUNT(*) AS something
                if (strpos($property, "AS") !== FALSE) {
                    $columns[] = $property;
                } else {
                    $propertyObject = $annotation->getPropertyByName($property);
                    if(!$propertyObject instanceof Property){
                        throw new HelperException("Unknown property '{$property}'.");
                    }
                    $columns[] = $propertyObject->getColumn();
                }
            }
            return $columns;
        }
        return $annotation->getColumnsToArray();
    }
}