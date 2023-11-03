<?php

namespace App\Helpers;

use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class IteratorAggregates extends NObject
{


    /**
     * @param $object null|\IteratorAggregate|\IteratorAggregate[]
     * @return array
     * @throws \Exception
     */
    public static function toArray($object)
    {
        $array = [];
        if ($object) {
            foreach (is_array($object) ? $object : [$object] as $o) {
                if (!is_a($o, \IteratorAggregate::class)) {
                    throw new \Exception("Object must be instance of '" . \IteratorAggregate::class . "'.");
                }
                $array[] = $o->toArray();
            }
        }
        return $array;
    }
}