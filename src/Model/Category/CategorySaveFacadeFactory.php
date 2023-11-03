<?php

namespace App\Category;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategorySaveFacadeFactory
{


    /**
     * @return CategorySaveFacade
     */
    public function create();
}