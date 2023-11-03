<?php

namespace App\Category;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryRepositoryFactory
{


    /**
     * @return CategoryRepository
     */
    public function create();
}