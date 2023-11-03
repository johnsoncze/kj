<?php

namespace App\Components\ProductParameterGroupForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupFormFactory
{


    /**
     * @return ProductParameterGroupForm
     */
    public function create();
}