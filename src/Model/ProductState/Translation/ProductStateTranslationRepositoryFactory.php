<?php

declare(strict_types = 1);

namespace App\ProductState\Translation;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductStateTranslationRepositoryFactory
{


    /**
     * @return ProductStateTranslationRepository
     */
    public function create();
}