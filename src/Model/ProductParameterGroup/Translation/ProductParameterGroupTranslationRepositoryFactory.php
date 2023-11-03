<?php

namespace App\ProductParameterGroup;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductParameterGroupTranslationRepositoryFactory
{


    /**
     * @return ProductParameterGroupTranslationRepository
     */
    public function create();
}