<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Catalog\Form;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CatalogFormFactory
{


    /**
     * @return CatalogForm
     */
    public function create();
}