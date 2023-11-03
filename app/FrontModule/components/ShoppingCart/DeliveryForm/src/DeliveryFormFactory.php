<?php

declare(strict_types = 1);

namespace App\FrontModule\Components\ShoppingCart\DeliveryForm;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface DeliveryFormFactory
{


    /**
     * @return DeliveryForm
     */
    public function create();
}