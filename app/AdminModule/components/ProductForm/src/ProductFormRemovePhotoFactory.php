<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductFormRemovePhotoFactory
{


    /**
     * @return ProductFormRemovePhoto
     */
    public function create();
}