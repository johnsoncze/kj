<?php

namespace App\AdminModule\Components\CategoryFiltrationCombinationParameterForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationCombinationParameterFormFactory
{


    /**
     * @return CategoryFiltrationCombinationParameterForm
     */
    public function create();
}