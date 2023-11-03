<?php

namespace App\Components\AdminModule\CategoryNavigationTree;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CategoryNavigationTreeFactory
{


    /**
     * @return CategoryNavigationTree
     */
    public function create();
}