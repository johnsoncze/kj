<?php

namespace App\Components\CategoryFiltrationSortForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationSortFormFactory
{


    /**
     * @return CategoryFiltrationSortForm
     */
    public function create();
}