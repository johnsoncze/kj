<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductRelatedForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductRelatedFormFactory
{


    /**
     * @return ProductRelatedForm
     */
    public function create();
}