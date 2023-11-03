<?php

declare(strict_types = 1);

namespace App\Catalog\Translation;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CatalogTranslationFacadeFactory
{


    /**
     * @return CatalogTranslationFacade
     */
    public function create();
}