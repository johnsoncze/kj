<?php

namespace App\ProductParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterTranslationRepositoryFactory
{


    /**
     * @return ProductParameterTranslationRepository
     */
    public function create();
}