<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Product\PhotoGallery;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PhotoGalleryFactory
{


    /**
     * @return PhotoGallery
     */
    public function create();
}