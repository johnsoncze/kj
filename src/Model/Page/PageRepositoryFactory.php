<?php

namespace App\Page;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageRepositoryFactory
{


    /**
     * @return PageRepository
     */
    public function create();
}