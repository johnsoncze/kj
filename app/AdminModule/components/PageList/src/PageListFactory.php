<?php

namespace App\Components\PageList;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageListFactory
{


    /**
     * @return PageList
     */
    public function create();
}