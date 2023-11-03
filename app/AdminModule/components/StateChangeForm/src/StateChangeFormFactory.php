<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\StateChangeForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface StateChangeFormFactory
{


    /**
     * @return StateChangeForm
     */
    public function create();
}