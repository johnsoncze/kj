<?php

declare(strict_types = 1);

namespace App\Product\Translation;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductTranslationRepositoryFactory
{


    /**
     * @return ProductTranslationRepository
     */
    public function create();
}