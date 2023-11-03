<?php

declare(strict_types = 1);

namespace App\Catalog;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CatalogFacadeFactory
{


    /**
     * @return CatalogFacade
     */
    public function create();
}