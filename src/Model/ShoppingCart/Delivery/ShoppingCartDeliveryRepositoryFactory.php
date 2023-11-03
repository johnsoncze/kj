<?php

declare(strict_types = 1);

namespace App\ShoppingCart\Delivery;

/**
 * @author Dusan Mlynarcik <dusan.mlynarcik@email.cz>
 */
interface ShoppingCartDeliveryRepositoryFactory
{


    /**
     * @return ShoppingCartDeliveryRepository
     */
    public function create();
}