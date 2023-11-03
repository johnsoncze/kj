<?php

namespace App\CategoryFiltration;

use App\Category\CategoryFiltrationRepository;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationRepositoryFactory
{


    /**
     * @return CategoryFiltrationRepository
     */
    public function create();
}