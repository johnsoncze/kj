<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Catalog\CatalogList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CatalogListFactory
{


    /**
     * @return CatalogList
     */
    public function create();
}