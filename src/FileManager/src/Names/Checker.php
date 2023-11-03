<?php

namespace App\Libs\FileManager;

use App\Libs\FileManager\Names\Name;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Checker extends NObject
{


    /**
     * @var Name
     */
    protected $name;

    /** @var string */
    protected $originalName;

    /**
     * @var string
     */
    protected $dir;

    /**
     * @var int
     */
    protected $int = 0;



    public function __construct(Name $name, $dir)
    {
        $this->name = $name;
        $this->dir = $dir;
    }



    /**
     * @return Name
     */
    public function check()
    {
        $filePath = $this->dir . "/" . $this->name->getFullName();
        if (is_file($filePath)) {
            if (!$this->originalName) {
                $this->originalName = $this->name->getName();
            }
            $this->int++;
            Helpers::rename($this->name, $this->originalName . "_" . $this->int, FALSE);
            return $this->check();
        }
        return $this->name;
    }
}