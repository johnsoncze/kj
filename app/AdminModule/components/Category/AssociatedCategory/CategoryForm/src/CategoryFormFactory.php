<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\AssociatedCategory\CategoryForm;

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