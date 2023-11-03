<?php

namespace App\Components\ProductParameterForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterFormFactory
{


    /**
     * @return ProductParameterForm
     */
    public function create();
}