<?php

namespace App\Libs\FileManager;

use App\Libs\FileManager\Names\Name;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Helpers extends NObject
{


    /**
     * @param Name $name
     * @param $newName
     * @param bool $addition
     * @return string
     */
    public static function rename(Name $name, $newName, $addition = FALSE)
    {
        $newName = $addition ? $name->getName() . $newName : $newName;
        $name->setName($newName);
        return $newName;
    }
}