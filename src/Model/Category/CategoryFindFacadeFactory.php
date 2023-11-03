<?php

declare(strict_types = 1);

namespace App\Category;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFindFacadeFactory
{


    /**
     * @return CategoryFindFacade
     */
    public function create();
}