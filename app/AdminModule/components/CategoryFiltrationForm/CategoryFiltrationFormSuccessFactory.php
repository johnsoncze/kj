<?php

namespace App\Components\AdminModule\CategoryFiltrationForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationFormSuccessFactory
{


    /**
     * @return CategoryFiltrationFormSuccess
     */
    public function create();
}