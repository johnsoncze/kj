<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Page\Menu;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface MenuFactory
{


    /**
     * @return Menu
     */
    public function create();
}