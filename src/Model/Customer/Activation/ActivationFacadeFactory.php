<?php

declare(strict_types = 1);

namespace App\Customer\Activation;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ActivationFacadeFactory
{


    /**
     * @return ActivationFacade
     */
    public function create() : ActivationFacade;
}