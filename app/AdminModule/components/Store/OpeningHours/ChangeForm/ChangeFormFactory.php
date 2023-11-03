<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\Store\OpeningHours\ChangeForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ChangeFormFactory
{


    /**
     * @return ChangeForm
     */
    public function create();
}