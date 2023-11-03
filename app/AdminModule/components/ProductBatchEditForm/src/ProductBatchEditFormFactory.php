<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductBatchEditForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductBatchEditFormFactory
{


    /**
     * @return ProductBatchEditForm
     */
    public function create();
}