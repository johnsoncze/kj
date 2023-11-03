<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductSearchEngineForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductSearchEngineFormFactory
{


    /**
     * @return ProductSearchEngineForm
     */
    public function create();
}