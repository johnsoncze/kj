<?php

namespace App\Libs\FileManager\Names;

use App\NObject;


/**
 * @method setName($name)
 * @method getName()
 * @method setExt($ext)
 * @method getExt()
 */
class Name extends NObject
{


    /** @var string */
    protected $name;

    /** @var string */
    protected $ext;



    /**
     * @param $name
     * @return Name
     */
    public static function create($name)
    {
        $nameObject = new Name();
        $nameObject->setName(pathinfo($name, PATHINFO_FILENAME));
        $nameObject->setExt(pathinfo($name, PATHINFO_EXTENSION));
        return $nameObject;
    }



    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->name . "." . $this->ext;
    }

}