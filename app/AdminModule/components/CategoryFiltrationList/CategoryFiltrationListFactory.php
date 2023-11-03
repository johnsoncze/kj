<?php

namespace App\Components\AdminModule\CategoryFiltrationList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationListFactory
{


    /**
     * @return CategoryFiltrationList
     */
    public function create();
}