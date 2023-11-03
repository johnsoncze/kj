<?php

namespace App\CategoryFiltration;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationSortFacadeFactory
{


    /**
     * @return CategoryFiltrationSortFacade
     */
    public function create();
}