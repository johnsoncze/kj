<?php

namespace App\Components\CategoryList;

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