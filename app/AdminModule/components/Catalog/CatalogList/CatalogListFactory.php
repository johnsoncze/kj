<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Catalog\CatalogList;

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