<?php

namespace App\Components\CategoryFiltrationSortForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationSortFormSuccessFactory
{


    /**
     * @return CategoryFiltrationSortFormSuccess
     */
    public function create();
}