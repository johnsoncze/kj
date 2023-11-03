<?php

declare(strict_types = 1);

namespace App\Product\AdditionalPhoto;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductAdditionalPhotoRepositoryFactory
{


    /**
     * @return ProductAdditionalPhotoRepository
     */
    public function create();
}