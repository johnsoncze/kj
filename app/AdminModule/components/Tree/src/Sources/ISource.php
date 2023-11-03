<?php

namespace App\Components\Tree\Sources;

use App\Components\Tree\Tree;


/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ISource
{


    /**
     * Apply source to Tree
     * @param $tree Tree
     * @return Tree
     */
    public function apply(Tree $tree) : Tree;
}