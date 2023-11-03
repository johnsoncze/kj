<?php

declare(strict_types = 1);

namespace App\Product\AdditionalPhoto;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductAdditionalPhotoSaveFacadeFactory
{


    /**
     * @return ProductAdditionalPhotoSaveFacade
     */
    public function create();
}