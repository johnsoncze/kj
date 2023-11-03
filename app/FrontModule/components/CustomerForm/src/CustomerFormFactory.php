<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\CustomerForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface CustomerFormFactory
{


    /**
     * @return CustomerForm
     */
    public function create();
}