<?php

declare(strict_types = 1);

namespace App\Helpers;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Objects extends NObject
{


    /**
     * @param array $objects
     * @param string $expectClass
     * @return array
     * @throws ObjectsException
     */
    public static function checkInstanceOf(array $objects, string $expectClass) : array
    {
        foreach ($objects as $object) {
            self::checkObject($object);
            if (!$object instanceof $expectClass) {
                throw new ObjectsException(sprintf("Object must be instance of '%s'. '%s' given.",
                    $expectClass, get_class($object)));
            }
        }

        return $objects;
    }



    /**
     * @param $object
     * @return mixed
     * @throws ObjectsException
     */
    public static function checkObject($object)
    {
        if (!is_object($object)) {
            throw new ObjectsException(sprintf("Parameter must be object. '%s' given.", gettype($object)));
        }

        return $object;
    }
}