<?php

namespace App\Libs\FileManager\Thumbnails;

use App\Libs\FileManager\Checker;
use App\Libs\FileManager\Names\Name;
use App\NObject;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Save extends NObject
{


    /** @var ThumbnailDTO */
    protected $thumbnailDTO;

    /** @var string */
    protected $dir;



    public function __construct(ThumbnailDTO $thumbnailDTO, $dir)
    {
        $this->thumbnailDTO = $thumbnailDTO;
        $this->dir = $dir;
    }



    /**
     * @param $overwrite bool
     * @return ThumbnailDTO
     */
    public function process($overwrite = FALSE)
    {
        @mkdir($this->dir, 0777, TRUE);

        if ($overwrite === FALSE) {
            $checker = new Checker(Name::create($this->thumbnailDTO->getName()), $this->dir);
            $name = $checker->check();
            $this->thumbnailDTO->setName($name->getFullName());
        }

        $this->thumbnailDTO->getImage()->save($this->dir . "/" . $this->thumbnailDTO->getName());
        return $this->thumbnailDTO;
    }
}