<?php

namespace App\AdminModule\Components\ProductParameterSortForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterSortFormFactory
{


    /**
     * @return ProductParameterSortForm
     */
    public function create();
}