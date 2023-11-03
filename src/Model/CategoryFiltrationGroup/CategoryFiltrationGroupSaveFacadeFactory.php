<?php

namespace App\CategoryFiltrationGroup;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationGroupSaveFacadeFactory
{


    /**
     * @return CategoryFiltrationGroupSaveFacade
     */
    public function create();
}