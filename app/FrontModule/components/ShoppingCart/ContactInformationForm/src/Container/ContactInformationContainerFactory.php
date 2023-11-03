<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\ContactInformationForm\Container;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ContactInformationContainerFactory
{


    /**
     * @return ContactInformationContainer
     */
    public function create();
}