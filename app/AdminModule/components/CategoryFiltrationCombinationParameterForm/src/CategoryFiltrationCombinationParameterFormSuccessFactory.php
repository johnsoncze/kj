<?php

namespace App\AdminModule\Components\CategoryFiltrationCombinationParameterForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryFiltrationCombinationParameterFormSuccessFactory
{


    /**
     * @return CategoryFiltrationCombinationParameterFormSuccess
     */
    public function create();
}