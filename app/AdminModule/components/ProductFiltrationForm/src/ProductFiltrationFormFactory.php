<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\ProductFiltrationForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ProductFiltrationFormFactory
{


    /**
     * @return ProductFiltrationForm
     */
    public function create();
}