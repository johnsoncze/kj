<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\ContactInformationForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ContactInformationFormFactory
{


    /**
     * @return ContactInformationForm
     */
    public function create();
}