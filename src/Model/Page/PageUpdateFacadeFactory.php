<?php

namespace App\Page;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageUpdateFacadeFactory
{


    /**
     * @return PageUpdateFacade
     */
    public function create();
}