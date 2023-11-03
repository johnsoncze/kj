<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\ButtonNavigation;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ButtonNavigationFactory
{


    /**
     * @return ButtonNavigation
     */
    public function create();
}