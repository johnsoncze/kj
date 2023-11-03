<?php

namespace App\Helpers;

use App\NObject;
use Nette\Utils\Strings;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Urls extends NObject
{


    /**
     * Create unique url
     * @param $value string
     * @return string
     */
    public static function createUniqueUrl($value)
    {
        return strtotime("now") . "-" . self::createUrl($value);
    }



    /**
     * Create url
     * @param $value string
     * @return string
     */
    public static function createUrl($value)
    {
        return Strings::webalize($value);
    }

}