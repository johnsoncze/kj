<?php

namespace App\Components\ProductParameterForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterFormSuccessFactory
{


    /**
     * @return ProductParameterFormSuccess
     */
    public function create();
}