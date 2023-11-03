<?php

namespace App\ProductParameterGroup;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupTranslationSaveFacadeFactory
{


    /**
     * @return ProductParameterGroupTranslationSaveFacade
     */
    public function create();
}