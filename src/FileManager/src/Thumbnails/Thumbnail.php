<?php

namespace App\Libs\FileManager\Thumbnails;

use App\Libs\FileManager\FileManager;
use App\NObject;
use Nette\Utils\Image;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Thumbnail extends NObject
{


    /** @var FileManager */
    protected $fileManager;

    /** @var string */
    protected $originName;

    /** @var int */
    protected $width;

    /** @var int */
    protected $height;



    public function __construct(FileManager $fileManager, $originName, $width, $height)
    {
        $this->fileManager = $fileManager;
        $this->originName = $originName;
        $this->width = $width;
        $this->height = $height;
    }



    /**
     * @return Image
     */
    protected function loadOriginFile()
    {
        return \Nette\Utils\Image::fromFile($this->fileManager->getDir()->getFullDir() . "/" . $this->originName);
    }



    /**
     * @return ThumbnailDTO
     */
    public function save()
    {
        $originImage = $this->loadOriginFile();
        Helpers::resize($originImage, $this->width, $this->height);
        $thumbnailDTO = new ThumbnailDTO($originImage, Helpers::getName($this->originName, $this->width, $this->height));
        $save = new Save($thumbnailDTO, $this->fileManager->getDir()->getFullDir());
        $save->process();
        return $thumbnailDTO;
    }



    /**
     * @return string
     */
    public function get()
    {
        $dir = $this->fileManager->getDir();
        $name = Helpers::getName($this->originName, $this->width, $this->height);
        $file = $dir->getFullDir() . "/" . $name;
        if (!is_file($file)) {
            $originalFile = $this->fileManager->getDir()->getFullDir() . "/" . $this->originName;
            if (is_file($originalFile)) {
                //TODO: TMP workaround
                $thumbnailDTO = $this->save();
                $name = $thumbnailDTO->getName();
            }
        }
        return $dir->getBaseDir() . ($dir->getFolder() ? "/" . $dir->getFolder() : "") . "/" . $name;
    }
}