<?php

namespace App\CategoryFiltration;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationRemoveFacadeFactory
{


    /**
     * @return CategoryFiltrationRemoveFacade
     */
    public function create();
}