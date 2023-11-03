<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\AssociatedCategory\CategoryList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryListFactory
{


    /**
     * @return CategoryList
     */
    public function create();
}