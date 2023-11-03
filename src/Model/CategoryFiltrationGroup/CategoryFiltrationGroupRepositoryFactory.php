<?php

namespace App\CategoryFiltrationGroup;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationGroupRepositoryFactory
{


    /**
     * @return CategoryFiltrationGroupRepository
     */
    public function create();
}