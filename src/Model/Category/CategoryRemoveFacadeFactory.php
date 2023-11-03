<?php

namespace App\Category;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryRemoveFacadeFactory
{


    /**
     * @return CategoryRemoveFacade
     */
    public function create();
}