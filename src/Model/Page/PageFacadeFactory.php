<?php

declare(strict_types = 1);

namespace App\Page;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PageFacadeFactory
{


    /**
     * @return PageFacade
     */
    public function create();
}