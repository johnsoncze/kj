<?php

namespace App\ProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterTranslationSaveFacadeFactory
{


    /**
     * @return ProductParameterTranslationSaveFacade
     */
    public function create();
}