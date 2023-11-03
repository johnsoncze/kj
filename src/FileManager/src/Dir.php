<?php

namespace App\Libs\FileManager;

use App\NObject;


/**
 * @method setBaseDir($baseDir)
 * @method getBaseDir()
 * @method setDir($dir)
 * @method getDir()
 * @method setFolder($folder)
 * @method getFolder()
 */
class Dir extends NObject
{


    /** @var string */
    protected $baseDir;

    /** @var string */
    protected $dir;

    /** @var string */
    protected $folder;



    /**
     * @param array $params
     * @return Dir
     */
    public static function create(array $params)
    {
        $dir = new Dir();
        $dir->setBaseDir($params["baseDir"]);
        $dir->setDir($params["dir"]);
        if (isset($params["folder"])) {
            $dir->setFolder($params["folder"]);
        }
        return $dir;
    }



    /**
     * @return string
     */
    public function getFullDir()
    {
        return $this->dir . "/" . $this->folder;
    }
}