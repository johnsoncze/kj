<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Category\Filtration\Combination\SortForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface SortFormFactory
{


    /**
     * @return SortForm
     */
    public function create();
}