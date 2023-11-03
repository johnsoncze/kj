<?php

namespace App\AdminModule\Components\ProductParameterSortForm;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterSortFormSuccessFactory
{


    /**
     * @return ProductParameterSortFormSuccess
     */
    public function create();
}