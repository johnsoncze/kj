<?php

namespace App\Libs\FileManager\Thumbnails;

use App\NObject;
use Nette\Utils\Image;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
class Helpers extends NObject
{


    /**
     * @param $originName
     * @param $width
     * @param $height
     * @return string
     */
    public static function getName($originName, $width, $height)
    {
        return $width . "_" . $height . "_" . $originName;
    }



    /**
     * @param Image $image
     * @param $width int
     * @param $height int
     * @return Image
     */
    public static function resize(Image $image, $width, $height)
    {
        $image->resize($width, $height, Image::FIT | Image::SHRINK_ONLY);
        return $image;
    }
}