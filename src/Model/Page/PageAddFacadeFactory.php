<?php

namespace App\Page;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageAddFacadeFactory
{


    /**
     * @return PageAddFacade
     */
    public function create();
}