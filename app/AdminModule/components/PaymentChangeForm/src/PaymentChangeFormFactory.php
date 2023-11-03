<?php

declare(strict_types = 1);

namespace App\AdminModule\Components\PaymentChangeForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface PaymentChangeFormFactory
{


    /**
     * @return PaymentChangeForm
     */
    public function create();
}
