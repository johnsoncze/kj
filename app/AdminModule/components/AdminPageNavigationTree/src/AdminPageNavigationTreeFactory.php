<?php

namespace App\Components\AdminPageNavigationTree;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface AdminPageNavigationTreeFactory
{


    /**
     * @return AdminPageNavigationTree
     */
    public function create();
}