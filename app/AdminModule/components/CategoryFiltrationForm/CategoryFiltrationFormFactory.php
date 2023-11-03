<?php

namespace App\Components\AdminModule\CategoryFiltrationForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationFormFactory
{


    /**
     * @return CategoryFiltrationForm
     */
    public function create();
}