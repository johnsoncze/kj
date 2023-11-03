<?php

namespace App\Helpers;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Arrays extends \Nette\Utils\Arrays
{


    /**
     * Return pair
     * @param $array array
     * @param $key string name of key
     * @param $value string name of value
     * @param $start array start pair
     * @return bool|array
     */
    public static function toPair(array $array, $key, $value, $start = null)
    {
        if (!$array) {
            return false;
        }
        $pair = [];
        if ($start) {
            foreach ($start as $n => $v) {
                $pair[$n] = $v;
            }
        }
        foreach ($array as $v) {
            $pair[$v[$key]] = $v[$value];
        }
        return $pair;
    }



    /**
     * Return one property.
     * @param $array array
     * @param $key string
     * @return array
     */
    public static function getOneValue(array $array, string $key) : array
    {
        $arr = [];
        foreach ($array as $k => $v) {
            $arr[] = $v[$key];
        }
        return $arr;
    }
}