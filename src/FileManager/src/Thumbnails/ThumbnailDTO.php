<?php

namespace App\Libs\FileManager\Thumbnails;

use App\NObject;
use Nette\Utils\Image;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 *
 * @method setName($name)
 * @method getImage()
 * @method getName()
 */
class ThumbnailDTO extends NObject
{


    /**
     * @var Image
     */
    protected $image;

    /**
     * @var string
     */
    protected $name;



    public function __construct(Image $image, $name)
    {
        $this->image = $image;
        $this->name = $name;
    }
}