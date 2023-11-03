<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Menu\Header;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface HeaderFactory
{


    /**
     * @return Header
     */
    public function create();
}