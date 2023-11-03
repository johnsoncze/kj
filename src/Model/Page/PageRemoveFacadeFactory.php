<?php

namespace App\Page;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageRemoveFacadeFactory
{


    /**
     * @return PageRemoveFacade
     */
    public function create();
}