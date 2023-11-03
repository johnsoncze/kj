<?php

namespace App\Components\CategoryForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFormFactory
{


    /**
     * @return CategoryForm
     */
    public function create();
}