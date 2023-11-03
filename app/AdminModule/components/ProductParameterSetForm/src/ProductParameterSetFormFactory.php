<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductParameterSetForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterSetFormFactory
{


    /**
     * @return ProductParameterSetForm
     */
    public function create();
}