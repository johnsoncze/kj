<?php

namespace App\CategoryFiltrationGroupParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationGroupParameterSaveFacadeFactory
{


    /**
     * @return CategoryFiltrationGroupParameterSaveFacade
     */
    public function create();
}