<?php

namespace App\CategoryFiltrationGroupParameter;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationGroupParameterRepositoryFactory
{


    /**
     * @return CategoryFiltrationGroupParameterRepository
     */
    public function create();
}