<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\Registration\Form;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface RegistrationFormFactory
{


    /**
     * @return RegistrationForm
     */
    public function create();
}